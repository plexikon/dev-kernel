<?php
declare(strict_types=1);

namespace Plexikon\Kernel\Model\Customer;

use Plexikon\Chronicle\Support\Aggregate\HasAggregateRoot;
use Plexikon\Chronicle\Support\Contracts\Aggregate\AggregateId;
use Plexikon\Chronicle\Support\Contracts\Aggregate\AggregateRoot;
use Plexikon\Kernel\Model\Customer\Event\CustomerDisabled;
use Plexikon\Kernel\Model\Customer\Event\CustomerEmailChanged;
use Plexikon\Kernel\Model\Customer\Event\CustomerEnabled;
use Plexikon\Kernel\Model\Customer\Event\CustomerNameChanged;
use Plexikon\Kernel\Model\Customer\Event\CustomerPasswordChanged;
use Plexikon\Kernel\Model\Customer\Event\CustomerRegistered;
use Plexikon\Kernel\Model\Customer\Exception\CustomerNotEnabled;
use Plexikon\Kernel\Model\Customer\Value\BcryptEncodedPassword;
use Plexikon\Kernel\Model\Customer\Value\CustomerId;
use Plexikon\Kernel\Model\Customer\Value\CustomerStatus;
use Plexikon\Kernel\Model\Customer\Value\EmailAddress;
use Plexikon\Kernel\Model\Customer\Value\Name;

class Customer implements AggregateRoot
{
    use HasAggregateRoot;

    private ?EmailAddress $email;
    private ?Name $name;
    private ?BcryptEncodedPassword $password;
    private CustomerStatus $status;

    public static function register(CustomerId $customerId,
                                    EmailAddress $email,
                                    Name $name,
                                    BcryptEncodedPassword $password): self
    {
        $self = new self($customerId);

        $self->recordThat(CustomerRegistered::withData(
            $customerId, $email, $name, $password, CustomerStatus::REGISTERED()
        ));

        return $self;
    }

    public function changeEmail(EmailAddress $newEmail): void
    {
        $this->assertCustomerIsEnabled();

        if ($this->email->sameValueAs($newEmail)) {
            return;
        }

        $this->recordThat(CustomerEmailChanged::forCustomer($this->customerId(), $newEmail, $this->email));
    }

    public function changePassword(BcryptEncodedPassword $encodedPassword): void
    {
        $this->assertCustomerIsEnabled();

        if ($this->password->sameValueAs($encodedPassword)) {
            return;
        }

        $this->recordThat(CustomerPasswordChanged::forCustomer(
            $this->customerId(), $encodedPassword, $this->password)
        );
    }

    public function changeName(Name $newName): void
    {
        if($this->name->sameValueAs($newName)){
            return;
        }

        $this->recordThat(CustomerNameChanged::forCustomer($this->customerId(), $newName, $this->name));
    }

    public function markAsEnabled(): void
    {
        if ($this->status->sameValueAs(CustomerStatus::DISABLED())) {
            return;
        }

        $this->assertCustomerIsEnabled();

        $this->recordThat(CustomerEnabled::forCustomer($this->customerId(), CustomerStatus::DISABLED(), $this->status));
    }

    public function markAsDisabled(): void
    {
        $enabledStatus = CustomerStatus::ENABLED();

        if ($this->status->sameValueAs($enabledStatus)) {
            return;
        }

        $this->recordThat(CustomerEnabled::forCustomer($this->customerId(), $enabledStatus, $this->status));
    }

    /**
     * @return CustomerId|AggregateId
     */
    public function customerId(): CustomerId
    {
        return $this->aggregateId;
    }

    public function getEmail(): EmailAddress
    {
        return $this->email;
    }

    public function getName(): Name
    {
        return $this->name;
    }

    public function getPassword(): BcryptEncodedPassword
    {
        return $this->password;
    }

    public function getStatus(): CustomerStatus
    {
        return $this->status;
    }

    public function isEnabled(): bool
    {
        return $this->status->sameValueAs(CustomerStatus::ENABLED());
    }

    protected function assertCustomerIsEnabled(): void
    {
        if (!$this->isEnabled()) {
            throw CustomerNotEnabled::withId($this->customerId());
        }
    }

    protected function applyCustomerRegistered(CustomerRegistered $event): void
    {
        $this->email = $event->email();
        $this->password = $event->password();
        $this->status = $event->status();
        $this->name = $event->name();
    }

    protected function applyCustomerEmailChanged(CustomerEmailChanged $event): void
    {
        $this->email = $event->newEmail();
    }

    protected function applyCustomerNameChanged(CustomerNameChanged $event): void
    {
        $this->name = $event->newName();
    }

    protected function applyCustomerPasswordChanged(CustomerPasswordChanged $event): void
    {
        $this->password = $event->newPassword();
    }

    protected function applyCustomerEnabled(CustomerEnabled $event): void
    {
        $this->status = $event->newStatus();
    }

    protected function applyCustomerDisabled(CustomerDisabled $event): void
    {
        $this->status = $event->newStatus();
    }
}
