<?php
declare(strict_types=1);

namespace Plexikon\Kernel\Model\Account\Handler;

use Plexikon\Kernel\Model\Account\Command\RegisterAccount;
use Plexikon\Kernel\Model\Account\Account;
use Plexikon\Kernel\Model\Account\Exception\AccountAlreadyExists;
use Plexikon\Kernel\Model\Account\Repository\AccountCollection;
use Plexikon\Kernel\Model\Account\Service\CredentialEncoder;
use Plexikon\Kernel\Model\Account\Service\UniqueEmailAddress;

final class RegisterAccountHandler
{
    private AccountCollection $accountCollection;
    private UniqueEmailAddress $uniqueEmail;
    private CredentialEncoder $credentialEncoder;

    public function __construct(AccountCollection $accountCollection,
                                UniqueEmailAddress $uniqueEmail,
                                CredentialEncoder $credentialEncoder)
    {
        $this->accountCollection = $accountCollection;
        $this->uniqueEmail = $uniqueEmail;
        $this->credentialEncoder = $credentialEncoder;
    }

    public function command(RegisterAccount $command): void
    {
        $accountId = $command->accountId();

        if ($account = $this->accountCollection->get($accountId)) {
            throw AccountAlreadyExists::withId($accountId);
        }

        $email = $command->email();

        if ($customerExists = ($this->uniqueEmail)($email)) {
            throw AccountAlreadyExists::withEmail($email);
        }

        $encodedPassword = $this->credentialEncoder->encode($command->clearPassword());

        $account = Account::register($accountId, $email, $command->name(), $encodedPassword);

        $this->accountCollection->store($account);
    }
}
