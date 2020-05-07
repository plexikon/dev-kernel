<?php

namespace Plexikon\Kernel\Model\Account\Repository;

use Plexikon\Kernel\Model\Account\Account;
use Plexikon\Kernel\Model\Account\Value\AccountId;

interface AccountCollection
{
    /**
     * @param AccountId $accountId
     * @return Account|null
     */
    public function get(AccountId $accountId): ?Account;

    /**
     * @param Account $customer
     */
    public function store(Account $customer): void;
}
