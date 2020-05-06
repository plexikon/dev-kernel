<?php
declare(strict_types=1);

namespace Plexikon\Kernel\Model\Customer\Event;

use Plexikon\Chronicle\Aggregate\AggregateChanged;
use Plexikon\Kernel\Model\Customer\Value\CustomerId;
use Plexikon\Kernel\Model\Customer\Value\EncodedPassword;

final class CustomerPasswordChanged extends AggregateChanged
{
    private ?EncodedPassword $newPassword;
    private ?EncodedPassword $oldPassword;

    public static function forCustomer(CustomerId $customerId, EncodedPassword $newPassword, EncodedPassword $oldPassword): self
    {
        $self = self::occur($customerId->toString(), [
            'new_password' => $newPassword->getValue(),
            'old_password' => $oldPassword->getValue(),
            'password_hash' => $newPassword->getHash()
        ]);

        $self->newPassword = $newPassword;
        $self->oldPassword = $oldPassword;

        return $self;
    }

    public function newPassword(): EncodedPassword
    {
        return $this->newPassword ?? EncodedPassword::fromString(
                $this->payload['new_password'],
                $this->payload['password_hash']
            );
    }
}
