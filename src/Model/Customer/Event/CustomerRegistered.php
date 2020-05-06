<?php
declare(strict_types=1);

namespace Plexikon\Kernel\Model\Customer\Event;

use Plexikon\Chronicle\Aggregate\AggregateChanged;
use Plexikon\Kernel\Model\Customer\Value\BcryptEncodedPassword;
use Plexikon\Kernel\Model\Customer\Value\CustomerId;
use Plexikon\Kernel\Model\Customer\Value\CustomerStatus;
use Plexikon\Kernel\Model\Customer\Value\EmailAddress;

final class CustomerRegistered extends AggregateChanged
{
    private ?EmailAddress $email;
    private ?BcryptEncodedPassword $password;
    private ?CustomerStatus $status;

    public static function withData(CustomerId $customerId,
                                    EmailAddress $email,
                                    BcryptEncodedPassword $password,
                                    CustomerStatus $status): self
    {
        $self = self::occur($customerId->toString(), [
            'email' => $email->getValue(),
            'password' => $password->getValue(),
            'status' => $status->getValue()
        ]);

        $self->email = $email;
        $self->password = $password;

        return $self;
    }

    public function getEmail(): EmailAddress
    {
        return $this->email ?? EmailAddress::fromString($this->payload['email']);
    }

    public function getPassword(): BcryptEncodedPassword
    {
        return $this->password ?? BcryptEncodedPassword::fromString(
                $this->payload['password']
            );
    }

    public function getStatus(): CustomerStatus
    {
        return $this->status ?? CustomerStatus::byValue($this->payload['status']);
    }
}
