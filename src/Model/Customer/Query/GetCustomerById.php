<?php
declare(strict_types=1);

namespace Plexikon\Kernel\Model\Customer\Query;

use Plexikon\Kernel\Model\Customer\Value\CustomerId;

final class GetCustomerById
{
    private string $customerId;

    public function __construct(string $customerId)
    {
        $this->customerId = $customerId;
    }

    public function getCustomerId(): CustomerId
    {
        return CustomerId::fromString($this->customerId);
    }
}
