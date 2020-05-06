<?php
declare(strict_types=1);

namespace Plexikon\Kernel\Model\Customer\Event;

use Plexikon\Chronicle\Aggregate\AggregateChanged;
use Plexikon\Kernel\Model\Customer\Value\BcryptEncodedPassword;
use Plexikon\Kernel\Model\Customer\Value\CustomerId;
use Plexikon\Kernel\Model\Customer\Value\EmailAddress;

final class CustomerRegistered extends AggregateChanged
{
    private ?EmailAddress $email;
    private ?BcryptEncodedPassword $password;

    public static function withData(CustomerId $customerId, EmailAddress $email, BcryptEncodedPassword $password): self
    {
        $self = self::occur($customerId->toString(), [
            'email' => $email->getValue(),
            'password' => $password->getValue()
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
}
