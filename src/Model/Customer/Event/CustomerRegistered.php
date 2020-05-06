<?php
declare(strict_types=1);

namespace Plexikon\Kernel\Model\Customer\Event;

use Plexikon\Chronicle\Aggregate\AggregateChanged;
use Plexikon\Kernel\Model\Customer\Value\BcryptEncodedPassword;
use Plexikon\Kernel\Model\Customer\Value\CustomerId;
use Plexikon\Kernel\Model\Customer\Value\CustomerStatus;
use Plexikon\Kernel\Model\Customer\Value\EmailAddress;
use Plexikon\Kernel\Model\Customer\Value\Name;

final class CustomerRegistered extends AggregateChanged
{
    private ?EmailAddress $email;
    private ?Name $name;
    private ?BcryptEncodedPassword $password;
    private ?CustomerStatus $status;

    public static function withData(CustomerId $customerId,
                                    EmailAddress $email,
                                    Name $name,
                                    BcryptEncodedPassword $password,
                                    CustomerStatus $status): self
    {
        $self = self::occur($customerId->toString(), [
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

    public function status(): CustomerStatus
    {
        return $this->status ?? CustomerStatus::byValue($this->payload['status']);
    }
}
