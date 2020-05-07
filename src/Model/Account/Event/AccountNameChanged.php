<?php
declare(strict_types=1);

namespace Plexikon\Kernel\Model\Account\Event;

use Plexikon\Chronicle\Aggregate\AggregateChanged;
use Plexikon\Kernel\Model\Account\Value\AccountId;
use Plexikon\Kernel\Model\Account\Value\Name;

final class AccountNameChanged extends AggregateChanged
{
    private ?Name $newName;
    private ?Name $oldName;

    public static function forCustomer(AccountId $accountId, Name $newName, Name $oldName)
    {
        $self = self::occur($accountId->getValue(),[
            'new_name' => $newName->getValue(),
            'old_name' => $oldName->getValue()
        ]);

        $self->newName = $newName;
        $self->oldName = $oldName;

        return $self;
    }

    public function newName(): Name
    {
        return $this->newName ?? Name::fromString($this->payload['new_name']);
    }

    public function oldName(): Name
    {
        return $this->oldName ?? Name::fromString($this->payload['old_name']);
    }
}
