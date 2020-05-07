<?php
declare(strict_types=1);

namespace Plexikon\Kernel\Model\Account;

use Plexikon\Chronicle\Support\Aggregate\HasAggregateRoot;
use Plexikon\Chronicle\Support\Contracts\Aggregate\AggregateId;
use Plexikon\Chronicle\Support\Contracts\Aggregate\AggregateRoot;
use Plexikon\Kernel\Model\Account\Event\AccountDisabled;
use Plexikon\Kernel\Model\Account\Event\AccountEmailChanged;
use Plexikon\Kernel\Model\Account\Event\AccountEnabled;
use Plexikon\Kernel\Model\Account\Event\AccountNameChanged;
use Plexikon\Kernel\Model\Account\Event\AccountPasswordChanged;
use Plexikon\Kernel\Model\Account\Event\AccountRegistered;
use Plexikon\Kernel\Model\Account\Exception\AccountNotEnabled;
use Plexikon\Kernel\Model\Account\Value\BcryptEncodedPassword;
use Plexikon\Kernel\Model\Account\Value\AccountId;
use Plexikon\Kernel\Model\Account\Value\AccountStatus;
use Plexikon\Kernel\Model\Account\Value\EmailAddress;
use Plexikon\Kernel\Model\Account\Value\Name;

class Account implements AggregateRoot
{
    use HasAggregateRoot;

    protected ?EmailAddress $email;
    protected ?Name $name;
    protected ?BcryptEncodedPassword $password;
    protected ?AccountStatus $status;

    public static function register(AccountId $accountId,
                                    EmailAddress $email,
                                    Name $name,
                                    BcryptEncodedPassword $password): self
    {
        $self = new self($accountId);

        $self->recordThat(AccountRegistered::withData(
            $accountId, $email, $name, $password, AccountStatus::REGISTERED()
        ));

        return $self;
    }

    public function changeEmail(EmailAddress $newEmail): void
    {
        $this->assertCustomerIsEnabled();

        if ($this->email->sameValueAs($newEmail)) {
            return;
        }

        $this->recordThat(AccountEmailChanged::forAccount($this->accountId(), $newEmail, $this->email));
    }

    public function changePassword(BcryptEncodedPassword $encodedPassword): void
    {
        $this->assertCustomerIsEnabled();

        if ($this->password->sameValueAs($encodedPassword)) {
            return;
        }

        $this->recordThat(AccountPasswordChanged::forAccount(
            $this->accountId(), $encodedPassword, $this->password)
        );
    }

    public function changeName(Name $newName): void
    {
        if($this->name->sameValueAs($newName)){
            return;
        }

        $this->recordThat(AccountNameChanged::forCustomer($this->accountId(), $newName, $this->name));
    }

    public function markAsEnabled(): void
    {
        if ($this->status->sameValueAs(AccountStatus::ENABLED())) {
            return;
        }

        $this->recordThat(AccountEnabled::forCustomer($this->accountId(), AccountStatus::ENABLED(), $this->status));
    }

    public function markAsDisabled(): void
    {
        $disabledStatus = AccountStatus::DISABLED();

        if ($this->status->sameValueAs($disabledStatus)) {
            return;
        }

        $this->recordThat(AccountEnabled::forCustomer($this->accountId(), AccountStatus::ENABLED(), $this->status));
    }

    /**
     * @return AccountId|AggregateId
     */
    public function accountId(): AccountId
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

    public function getStatus(): AccountStatus
    {
        return $this->status;
    }

    public function isEnabled(): bool
    {
        return $this->status->sameValueAs(AccountStatus::ENABLED());
    }

    protected function assertCustomerIsEnabled(): void
    {
        if (!$this->isEnabled()) {
            throw AccountNotEnabled::withId($this->accountId());
        }
    }

    protected function applyAccountRegistered(AccountRegistered $event): void
    {
        $this->email = $event->email();
        $this->password = $event->password();
        $this->status = $event->status();
        $this->name = $event->name();
    }

    protected function applyAccountEmailChanged(AccountEmailChanged $event): void
    {
        $this->email = $event->newEmail();
    }

    protected function applyAccountNameChanged(AccountNameChanged $event): void
    {
        $this->name = $event->newName();
    }

    protected function applyAccountPasswordChanged(AccountPasswordChanged $event): void
    {
        $this->password = $event->newPassword();
    }

    protected function applyAccountEnabled(AccountEnabled $event): void
    {
        $this->status = $event->newStatus();
    }

    protected function applyAccountDisabled(AccountDisabled $event): void
    {
        $this->status = $event->newStatus();
    }
}
