<?php
declare(strict_types=1);

namespace Plexikon\Kernel\Model\Account\Command;

use Plexikon\Kernel\Model\Account\Value\AccountId;
use Plexikon\Kernel\Model\Account\Value\Name;
use Plexikon\Reporter\Command;

final class AccountChangeName extends Command
{
    public static function withData(string $accountId, string $newName)
    {
        return new self([
            'account_id' => $accountId,
            'new_name' => $newName
        ]);
    }

    public function customerId(): AccountId
    {
        return AccountId::fromString($this->payload['account_id']);
    }

    public function newName(): Name
    {
        return Name::fromString($this->payload[' new_name']);
    }
}
