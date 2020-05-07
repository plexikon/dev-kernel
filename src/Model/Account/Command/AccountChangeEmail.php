<?php
declare(strict_types=1);

namespace Plexikon\Kernel\Model\Account\Command;

use Plexikon\Kernel\Model\Account\Value\AccountId;
use Plexikon\Kernel\Model\Account\Value\EmailAddress;
use Plexikon\Reporter\Command;

final class AccountChangeEmail extends Command
{
    public static function withData(string $accountId, string $newEmail): self
    {
        return new self([
            'account_id' => $accountId,
            'new_email' => $newEmail
        ]);
    }

    public function customerId(): AccountId
    {
        return AccountId::fromString($this->payload['account_id']);
    }

    public function newEmail(): EmailAddress
    {
        return EmailAddress::fromString($this->payload['new_email']);
    }
}
