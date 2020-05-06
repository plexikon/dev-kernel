<?php
declare(strict_types=1);

namespace Plexikon\Kernel\Model\Customer\Exception;

use Plexikon\Kernel\Model\Customer\Value\CustomerId;

final class CustomerNotEnabled extends CustomerStatusException
{
    public static function withId(CustomerId $customerId): self
    {
        return new self("Customer with id {$customerId->getValue()} is not enabled");
    }
}
