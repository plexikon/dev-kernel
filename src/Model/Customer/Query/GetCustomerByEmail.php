<?php
declare(strict_types=1);

namespace Plexikon\Kernel\Model\Customer\Query;

use Plexikon\Kernel\Model\Customer\Value\EmailAddress;

final class GetCustomerByEmail
{
    private string $email;

    public function __construct(string $email)
    {
        $this->email = $email;
    }

    public function getEmail(): EmailAddress
    {
        return EmailAddress::fromString($this->email);
    }
}
