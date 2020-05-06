<?php
declare(strict_types=1);

namespace Plexikon\Kernel\Model\Customer\Event;

use Plexikon\Chronicle\Aggregate\AggregateChanged;
use Plexikon\Kernel\Model\Customer\Value\BcryptEncodedPassword;
use Plexikon\Kernel\Model\Customer\Value\CustomerId;

final class CustomerPasswordChanged extends AggregateChanged
{
    private ?BcryptEncodedPassword $newPassword;
    private ?BcryptEncodedPassword $oldPassword;

    public static function forCustomer(CustomerId $customerId, BcryptEncodedPassword $newPassword, BcryptEncodedPassword $oldPassword): self
    {
        $self = self::occur($customerId->toString(), [
            'new_password' => $newPassword->getValue(),
            'old_password' => $oldPassword->getValue(),
        ]);

        $self->newPassword = $newPassword;
        $self->oldPassword = $oldPassword;

        return $self;
    }

    public function newPassword(): BcryptEncodedPassword
    {
        return $this->newPassword ?? BcryptEncodedPassword::fromString($this->payload['new_password']);
    }

    public function oldPassword(): BcryptEncodedPassword
    {
        return $this->oldPassword ?? BcryptEncodedPassword::fromString($this->payload['old_password']);
    }
}
