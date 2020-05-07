<?php
declare(strict_types=1);

namespace Plexikon\Kernel\Model\Account\Command;

use Plexikon\Kernel\Model\Account\Value\ClearPassword;
use Plexikon\Kernel\Model\Account\Value\ClearPasswordConfirmation;
use Plexikon\Kernel\Model\Account\Value\AccountId;
use Plexikon\Reporter\Command;

final class AccountChangePassword extends Command
{
    public static function withData(string $accountId,
                                    string $currentPassword,
                                    string $newPassword,
                                    string $newPasswordConfirmation): self
    {
        return new self([
            'account_id' => $accountId,
            'current_password' => $currentPassword,
            'new_password' => $newPassword,
            'new_password_confirmation' => $newPasswordConfirmation,
        ]);
    }

    public function accountId(): AccountId
    {
        return AccountId::fromString($this->payload['account_id']);
    }

    public function currentClearPassword(): ClearPassword
    {
        return ClearPassword::fromString($this->payload['current_password']);
    }

    public function newClearPasswordConfirmation(): ClearPasswordConfirmation
    {
        return ClearPasswordConfirmation::withConfirmation(
            $this->payload['new_password'],
            $this->payload['new_password_confirmation']
        );
    }
}
