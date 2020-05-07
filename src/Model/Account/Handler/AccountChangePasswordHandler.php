<?php
declare(strict_types=1);

namespace Plexikon\Kernel\Model\Account\Handler;

use Plexikon\Kernel\Model\Account\Command\AccountChangePassword;
use Plexikon\Kernel\Model\Account\Exception\BadCredentials;
use Plexikon\Kernel\Model\Account\Exception\AccountNotFound;
use Plexikon\Kernel\Model\Account\Repository\AccountCollection;
use Plexikon\Kernel\Model\Account\Service\CredentialEncoder;
use Plexikon\Kernel\Model\Account\Value\BcryptEncodedPassword;

final class AccountChangePasswordHandler
{
    private AccountCollection $accountCollection;
    private CredentialEncoder $credentialEncoder;

    public function __construct(AccountCollection $accountCollection, CredentialEncoder $credentialEncoder)
    {
        $this->accountCollection = $accountCollection;
        $this->credentialEncoder = $credentialEncoder;
    }

    public function command(AccountChangePassword $command): void
    {
        $accountId = $command->accountId();

        if (!$acccount = $this->accountCollection->get($accountId)) {
            throw AccountNotFound::withId($accountId);
        }

        $encodedPassword = $this->encodeValidatedPassword($command, $acccount->getPassword());

        $acccount->changePassword($encodedPassword);

        $this->accountCollection->store($acccount);
    }

    private function encodeValidatedPassword(AccountChangePassword $command, BcryptEncodedPassword $password): BcryptEncodedPassword
    {
        if (!$this->credentialEncoder->check($command->currentClearPassword(), $password)) {
            throw new BadCredentials('Invalid password');
        }

        return $this->credentialEncoder->encode($command->newClearPasswordConfirmation());
    }
}
