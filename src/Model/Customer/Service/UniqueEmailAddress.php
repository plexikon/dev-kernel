<?php

namespace Plexikon\Kernel\Model\Customer\Service;

use Plexikon\Kernel\Model\Customer\Value\CustomerId;
use Plexikon\Kernel\Model\Customer\Value\EmailAddress;

interface UniqueEmailAddress
{
    /**
     * @param EmailAddress $email
     * @return CustomerId|null
     */
    public function __invoke(EmailAddress $email): ?CustomerId;
}
