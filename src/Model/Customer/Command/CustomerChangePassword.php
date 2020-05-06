<?php
declare(strict_types=1);

namespace Plexikon\Kernel\Model\Customer\Command;

use Plexikon\Kernel\Model\Customer\Value\ClearPassword;
use Plexikon\Kernel\Model\Customer\Value\ClearPasswordConfirmation;
use Plexikon\Kernel\Model\Customer\Value\CustomerId;
use Plexikon\Reporter\Command;

final class CustomerChangePassword extends Command
{
    public static function forCustomer(string $customerId,
                                       string $currentPassword,
                                       string $newPassword,
                                       string $newPasswordConfirmation): self
    {
        return new self([
            'customer_id' > $customerId,
            'current_password' => $currentPassword,
            'new_password' => $newPassword,
            'new_password_confirmation' => $newPasswordConfirmation,
        ]);
    }

    public function customerId(): CustomerId
    {
        return CustomerId::fromString($this->payload['customer_id']);
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
