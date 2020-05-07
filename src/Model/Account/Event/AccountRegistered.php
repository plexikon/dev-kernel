<?php
declare(strict_types=1);

namespace Plexikon\Kernel\Model\Account\Event;

use Plexikon\Chronicle\Aggregate\AggregateChanged;
use Plexikon\Kernel\Model\Account\Value\BcryptEncodedPassword;
use Plexikon\Kernel\Model\Account\Value\AccountId;
use Plexikon\Kernel\Model\Account\Value\AccountStatus;
use Plexikon\Kernel\Model\Account\Value\EmailAddress;
use Plexikon\Kernel\Model\Account\Value\Name;

final class AccountRegistered extends AggregateChanged
{
    private ?EmailAddress $email;
    private ?Name $name;
    private ?BcryptEncodedPassword $password;
    private ?AccountStatus $status;

    public static function withData(AccountId $accountId,
                                    EmailAddress $email,
                                    Name $name,
                                    BcryptEncodedPassword $password,
                                    AccountStatus $status): self
    {
        $self = self::occur($accountId->toString(), [
            'email' => $email->getValue(),
            'name' => $name->getValue(),
            'password' => $password->getValue(),
            'status' => $status->getValue()
        ]);

        $self->email = $email;
        $self->password = $password;
        $self->name = $name;
        $self->status = $status;

        return $self;
    }

    public function email(): EmailAddress
    {
        return $this->email ?? EmailAddress::fromString($this->payload['email']);
    }

    public function name(): Name
    {
        return $this->name ?? Name::fromString($this->payload['name']);
    }

    public function password(): BcryptEncodedPassword
    {
        return $this->password ?? BcryptEncodedPassword::fromString($this->payload['password']);
    }

    public function status(): AccountStatus
    {
        return $this->status ?? AccountStatus::byValue($this->payload['status']);
    }
}
