<?php
declare(strict_types=1);

namespace Plexikon\Kernel\Model\Account\Handler;

use Plexikon\Kernel\Model\Account\Command\AccountChangeEmail;
use Plexikon\Kernel\Model\Account\Exception\AccountAlreadyExists;
use Plexikon\Kernel\Model\Account\Exception\AccountNotFound;
use Plexikon\Kernel\Model\Account\Repository\AccountCollection;
use Plexikon\Kernel\Model\Account\Service\UniqueEmailAddress;

final class AccountChangeEmailHandler
{
    private AccountCollection $accountCollection;
    private UniqueEmailAddress $uniqueEmail;

    public function __construct(AccountCollection $accountCollection, UniqueEmailAddress $uniqueEmail)
    {
        $this->accountCollection = $accountCollection;
        $this->uniqueEmail = $uniqueEmail;
    }

    public function command(AccountChangeEmail $command): void
    {
        $accountId = $command->customerId();

        if (!$account = $this->accountCollection->get($accountId)) {
            throw AccountNotFound::withId($accountId);
        }

        $email = $command->newEmail();

        if ($account->getEmail()->sameValueAs($email)) {
            return;
        }

        if ($accountExists = ($this->uniqueEmail)($email)) {
            throw AccountAlreadyExists::withEmail($email);
        }

        $account->changeEmail($email);

        $this->accountCollection->store($account);
    }
}
