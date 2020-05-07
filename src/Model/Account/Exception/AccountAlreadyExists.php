<?php
declare(strict_types=1);

namespace Plexikon\Kernel\Model\Account\Exception;

use Plexikon\Kernel\Model\Account\Value\AccountId;
use Plexikon\Kernel\Model\Account\Value\EmailAddress;

class AccountAlreadyExists extends AccountException
{
    public static function withId(AccountId $accountId): self
    {
        return new self("Account with id {$accountId->toString()} already exists");
    }

    public static function withEmail(EmailAddress $email): self
    {
        return new self("Account with email {$email->getValue()} already exists");
    }
}
