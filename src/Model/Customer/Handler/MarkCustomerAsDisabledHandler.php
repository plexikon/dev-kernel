<?php
declare(strict_types=1);

namespace Plexikon\Kernel\Model\Customer\Handler;

use Plexikon\Kernel\Model\Customer\Command\MarkCustomerAsEnabled;
use Plexikon\Kernel\Model\Customer\Exception\CustomerNotFound;
use Plexikon\Kernel\Model\Customer\Repository\CustomerCollection;

final class MarkCustomerAsDisabledHandler
{
    private CustomerCollection $customerCollection;

    public function __construct(CustomerCollection $customerCollection)
    {
        $this->customerCollection = $customerCollection;
    }

    public function command(MarkCustomerAsEnabled $command): void
    {
        $customerId = $command->customerId();

        if (!$customer = $this->customerCollection->get($customerId)) {
            throw CustomerNotFound::withId($customerId);
        }

        $customer->markAsDisabled();

        $this->customerCollection->store($customer);
    }
}
