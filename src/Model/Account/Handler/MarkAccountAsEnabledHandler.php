<?php
declare(strict_types=1);

namespace Plexikon\Kernel\Model\Account\Handler;

use Plexikon\Kernel\Model\Account\Command\MarkAccountAsEnabled;
use Plexikon\Kernel\Model\Account\Exception\AccountNotFound;
use Plexikon\Kernel\Model\Account\Repository\AccountCollection;

final class MarkAccountAsEnabledHandler
{
    private AccountCollection $customerCollection;

    public function __construct(AccountCollection $customerCollection)
    {
        $this->customerCollection = $customerCollection;
    }

    public function command(MarkAccountAsEnabled $command): void
    {
        $accountId = $command->accountId();

        if(!$acccount = $this->customerCollection->get($accountId)){
            throw AccountNotFound::withId($accountId);
        }

        $acccount->markAsEnabled();

        $this->customerCollection->store($acccount);
    }
}
