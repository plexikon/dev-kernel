<?php
declare(strict_types=1);

namespace Plexikon\Kernel\Model\Customer\Command;

use Plexikon\Kernel\Model\Customer\Value\ClearPasswordConfirmation;
use Plexikon\Kernel\Model\Customer\Value\CustomerId;
use Plexikon\Kernel\Model\Customer\Value\EmailAddress;
use Plexikon\Reporter\Command;

final class RegisterCustomer extends Command
{
    public static function withData(string $customerId, string $email, string $password, string $passwordConfirmation): self
    {
        return new self([
            'customer_id' => $customerId,
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $passwordConfirmation
        ]);
    }

    public function customerId(): CustomerId
    {
        return CustomerId::fromString($this->payload['customer_id']);
    }

    public function email(): EmailAddress
    {
        return EmailAddress::fromString($this->payload['email']);
    }

    public function clearPassword(): ClearPasswordConfirmation
    {
        return ClearPasswordConfirmation::withConfirmation(
            $this->payload['password'],
            $this->payload['password_confirmation']
        );
    }
}
