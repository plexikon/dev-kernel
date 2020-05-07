<?php
declare(strict_types=1);

namespace Plexikon\Kernel\Model\Account\Event;

use Plexikon\Chronicle\Aggregate\AggregateChanged;
use Plexikon\Kernel\Model\Account\Value\AccountId;
use Plexikon\Kernel\Model\Account\Value\AccountStatus;

final class AccountDisabled extends AggregateChanged
{
    private ?AccountStatus $newStatus;
    private ?AccountStatus $oldStatus;

    public static function forAccount(AccountId $accountId, AccountStatus $newStatus, AccountStatus $oldStatus): self
    {
        $self = self::occur($accountId->toString(), [
            'new_status' => $newStatus->getValue(),
            'old_status' => $oldStatus->getValue()
        ]);

        $self->newStatus = $newStatus;
        $self->oldStatus = $oldStatus;

        return $self;
    }

    public function newStatus(): AccountStatus
    {
        return $this->newStatus ?? AccountStatus::byValue($this->payload['new_status']);
    }

    public function oldStatus(): AccountStatus
    {
        return $this->oldStatus ?? AccountStatus::byValue($this->payload['old_status']);
    }
}
