<?php
declare(strict_types=1);

namespace Plexikon\Kernel\Model\Customer\Event;

use Plexikon\Chronicle\Aggregate\AggregateChanged;
use Plexikon\Kernel\Model\Customer\Value\CustomerId;
use Plexikon\Kernel\Model\Customer\Value\EmailAddress;
use Plexikon\Kernel\Model\Customer\Value\EncodedPassword;

final class CustomerRegistered extends AggregateChanged
{
    private ?EmailAddress $email;
    private ?EncodedPassword $password;

    public static function withData(CustomerId $customerId, EmailAddress $email, EncodedPassword $password): self
    {
        $self = self::occur($customerId->toString(), [
            'email' => $email->getValue(),
            'password' => $password->getValue(),
            'password_hash' => $password->getHash()
        ]);

        $self->email = $email;
        $self->password = $password;

        return $self;
    }

    public function getEmail(): EmailAddress
    {
        return $this->email ?? EmailAddress::fromString($this->payload['email']);
    }

    public function getPassword(): EncodedPassword
    {
        return $this->password ?? EncodedPassword::fromString(
                $this->payload['password'],
                $this->payload['password_hash']
            );
    }
}
