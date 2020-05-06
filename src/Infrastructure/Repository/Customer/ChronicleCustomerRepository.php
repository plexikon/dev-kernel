<?php
declare(strict_types=1);

namespace Plexikon\Kernel\Infrastructure\Repository\Customer;

use Plexikon\Chronicle\Support\Contracts\Aggregate\AggregateRepository;
use Plexikon\Kernel\Model\Customer\Customer;
use Plexikon\Kernel\Model\Customer\Repository\CustomerCollection;
use Plexikon\Kernel\Model\Customer\Value\CustomerId;

final class ChronicleCustomerRepository implements CustomerCollection
{
    private AggregateRepository $aggregateRepository;

    public function __construct(AggregateRepository $aggregateRepository)
    {
        $this->aggregateRepository = $aggregateRepository;
    }

    public function get(CustomerId $customerId): ?Customer
    {
        /** @var Customer $customer */
        $customer = $this->aggregateRepository->retrieve($customerId);

        return $customer;
    }

    public function store(Customer $customer): void
    {
        $this->aggregateRepository->persist($customer);
    }

    public function getAggregateRepository(): AggregateRepository
    {
        return $this->aggregateRepository;
    }
}
