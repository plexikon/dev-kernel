<?php
declare(strict_types=1);

namespace Plexikon\Kernel\Model\Account\Event;

use Plexikon\Chronicle\Aggregate\AggregateChanged;
use Plexikon\Kernel\Model\Account\Value\AccountId;
use Plexikon\Kernel\Model\Account\Value\EmailAddress;

final class AccountEmailChanged extends AggregateChanged
{
    private ?EmailAddress $newEmail;
    private ?EmailAddress $oldEmail;

    public static function forAccount(AccountId $accountId,
                                      EmailAddress $newEmail,
                                      EmailAddress $oldEmail): self
    {
        $self = self::occur($accountId->toString(), [
            'new_email' => $newEmail->getValue(),
            'old_email' => $oldEmail->getValue()
        ]);

        $self->newEmail = $newEmail;
        $self->oldEmail = $oldEmail;

        return $self;
    }

    public function newEmail(): EmailAddress
    {
        return $this->newEmail ?? EmailAddress::fromString($this->payload['new_email']);
    }

    public function oldEmail(): EmailAddress
    {
        return $this->oldEmail ?? EmailAddress::fromString($this->payload['old_email']);
    }
}
