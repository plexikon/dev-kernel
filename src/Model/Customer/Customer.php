<?php
declare(strict_types=1);

namespace Plexikon\Kernel\Model\Customer;

use Plexikon\Chronicle\Support\Aggregate\HasAggregateRoot;
use Plexikon\Chronicle\Support\Contracts\Aggregate\AggregateId;
use Plexikon\Chronicle\Support\Contracts\Aggregate\AggregateRoot;
use Plexikon\Kernel\Model\Customer\Event\CustomerEmailChanged;
use Plexikon\Kernel\Model\Customer\Event\CustomerPasswordChanged;
use Plexikon\Kernel\Model\Customer\Event\CustomerRegistered;
use Plexikon\Kernel\Model\Customer\Value\CustomerId;
use Plexikon\Kernel\Model\Customer\Value\EmailAddress;
use Plexikon\Kernel\Model\Customer\Value\BcryptEncodedPassword;

class Customer implements AggregateRoot
{
    use HasAggregateRoot;

    private ?EmailAddress $email;
    private ?BcryptEncodedPassword $password;

    public static function register(CustomerId $customerId, EmailAddress $email, BcryptEncodedPassword $password): self
    {
        $self = new self($customerId);

        $self->recordThat(CustomerRegistered::withData($customerId, $email, $password));

        return $self;
    }

    public function changeEmail(EmailAddress $newEmail): void
    {
        if($this->email->sameValueAs($newEmail)){
            return;
        }

       $this->recordThat(CustomerEmailChanged::forCustomer($this->customerId(), $newEmail, $this->email));
    }

    public function changePassword(BcryptEncodedPassword $encodedPassword): void
    {
        if($this->password->sameValueAs($encodedPassword)){
            return;
        }

        $this->recordThat(CustomerPasswordChanged::forCustomer($this->customerId(), $encodedPassword, $this->password));
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

    public function getPassword(): BcryptEncodedPassword
    {
        return $this->password;
    }

    protected function applyCustomerRegistered(CustomerRegistered $event): void
    {
        $this->email = $event->getEmail();
        $this->password = $event->getPassword();
    }

    protected function applyCustomerEmailChanged(CustomerEmailChanged $event): void
    {
        $this->email = $event->getNewEmail();
    }

    protected function applyCustomerPasswordChanged(CustomerPasswordChanged $event): void
    {
        $this->password = $event->newPassword();
    }
}
