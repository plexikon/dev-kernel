<?php
declare(strict_types=1);

namespace Plexikon\Kernel\Model\Account\Exception;

use Plexikon\Kernel\Model\Account\Value\AccountId;

class AccountNotEnabled extends AccountStatusException
{
    public static function withId(AccountId $accountId): self
    {
        return new self("Account with id {$accountId->getValue()} is not enabled");
    }
}
