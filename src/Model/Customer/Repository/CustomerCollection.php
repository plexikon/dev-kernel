<?php

namespace Plexikon\Kernel\Model\Customer\Repository;

use Plexikon\Kernel\Model\Customer\Customer;
use Plexikon\Kernel\Model\Customer\Value\CustomerId;

interface CustomerCollection
{
    /**
     * @param CustomerId $customerId
     * @return Customer|null
     */
    public function get(CustomerId $customerId): ?Customer;

    /**
     * @param Customer $customer
     */
    public function store(Customer $customer): void;
}
