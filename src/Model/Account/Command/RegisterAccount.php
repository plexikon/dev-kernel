<?php
declare(strict_types=1);

namespace Plexikon\Kernel\Model\Account\Command;

use Plexikon\Kernel\Model\Account\Value\ClearPasswordConfirmation;
use Plexikon\Kernel\Model\Account\Value\AccountId;
use Plexikon\Kernel\Model\Account\Value\EmailAddress;
use Plexikon\Kernel\Model\Account\Value\Name;
use Plexikon\Reporter\Command;

final class RegisterAccount extends Command
{
    public static function withData(string $accountId,
                                    string $email,
                                    string $name,
                                    string $password,
                                    string $passwordConfirmation): self
    {
        return new self([
            'account_id' => $accountId,
            'email' => $email,
            'name' => $name,
            'password' => $password,
            'password_confirmation' => $passwordConfirmation
        ]);
    }

    public function accountId(): AccountId
    {
        return AccountId::fromString($this->payload['account_id']);
    }

    public function email(): EmailAddress
    {
        return EmailAddress::fromString($this->payload['email']);
    }

    public function name(): Name
    {
        return Name::fromString($this->payload['name']);
    }

    public function clearPassword(): ClearPasswordConfirmation
    {
        return ClearPasswordConfirmation::withConfirmation(
            $this->payload['password'],
            $this->payload['password_confirmation']
        );
    }
}
