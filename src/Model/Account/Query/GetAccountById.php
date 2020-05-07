<?php
declare(strict_types=1);

namespace Plexikon\Kernel\Model\Account\Query;

use Plexikon\Kernel\Model\Account\Value\AccountId;

final class GetAccountById
{
    private string $accountId;

    public function __construct(string $accountId)
    {
        $this->accountId = $accountId;
    }

    public function accountId(): AccountId
    {
        return AccountId::fromString($this->accountId);
    }
}
