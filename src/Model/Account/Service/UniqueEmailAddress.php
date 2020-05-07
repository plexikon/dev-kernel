<?php

namespace Plexikon\Kernel\Model\Account\Service;

use Plexikon\Kernel\Model\Account\Value\AccountId;
use Plexikon\Kernel\Model\Account\Value\EmailAddress;

interface UniqueEmailAddress
{
    /**
     * @param EmailAddress $email
     * @return AccountId|null
     */
    public function __invoke(EmailAddress $email): ?AccountId;
}
