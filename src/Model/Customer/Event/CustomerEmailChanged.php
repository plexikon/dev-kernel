<?php
declare(strict_types=1);

namespace Plexikon\Kernel\Model\Customer\Event;

use Plexikon\Chronicle\Aggregate\AggregateChanged;
use Plexikon\Kernel\Model\Customer\Value\CustomerId;
use Plexikon\Kernel\Model\Customer\Value\EmailAddress;

final class CustomerEmailChanged extends AggregateChanged
{
    private ?EmailAddress $newEmail;
    private ?EmailAddress $oldEmail;

    public static function forCustomer(CustomerId $customerId, EmailAddress $newEmail, EmailAddress $oldEmail): self
    {
        $self = self::occur($customerId->toString(), [
            'new_email' => $newEmail->getValue(),
            'old_email' => $oldEmail->getValue()
        ]);

        $self->newEmail = $newEmail;
        $self->oldEmail = $oldEmail;

        return $self;
    }

    public function getNewEmail(): EmailAddress
    {
        return $this->newEmail ?? EmailAddress::fromString($this->payload['new_email']);
    }
}
