<?php
declare(strict_types=1);

namespace Plexikon\Kernel\Model\Account\Query;

use Plexikon\Kernel\Model\Account\Value\EmailAddress;

final class GetAccountByEmail
{
    private string $email;

    public function __construct(string $email)
    {
        $this->email = $email;
    }

    public function email(): EmailAddress
    {
        return EmailAddress::fromString($this->email);
    }
}
