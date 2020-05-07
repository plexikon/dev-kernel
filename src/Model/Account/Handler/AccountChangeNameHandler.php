<?php
declare(strict_types=1);

namespace Plexikon\Kernel\Model\Account\Handler;

use Plexikon\Kernel\Model\Account\Command\AccountChangeName;
use Plexikon\Kernel\Model\Account\Exception\AccountNotFound;
use Plexikon\Kernel\Model\Account\Repository\AccountCollection;

final class AccountChangeNameHandler
{
    private AccountCollection $accountCollection;

    public function __construct(AccountCollection $accountCollection)
    {
        $this->accountCollection = $accountCollection;
    }

    public function command(AccountChangeName $command): void
    {
        $accountId = $command->customerId();

        if (!$account = $this->accountCollection->get($accountId)) {
            throw AccountNotFound::withId($accountId);
        }

        $account->changeName($command->newName());

        $this->accountCollection->store($account);
    }
}
