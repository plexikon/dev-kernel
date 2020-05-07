<?php
declare(strict_types=1);

namespace Plexikon\Kernel\Model\Account\Command;

use Plexikon\Kernel\Model\Account\Value\AccountId;
use Plexikon\Reporter\Command;

final class MarkAccountAsEnabled extends Command
{
    public function withData(string $accountId): self
    {
        return new self(['account_id' => $accountId]);
    }

    public function accountId(): AccountId
    {
        return AccountId::fromString($this->payload['account_id']);
    }
}
