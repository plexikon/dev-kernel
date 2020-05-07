<?php
declare(strict_types=1);

namespace Plexikon\Kernel\Infrastructure\Repository\Customer;

use Plexikon\Chronicle\Support\Contracts\Aggregate\AggregateRepository;
use Plexikon\Kernel\Model\Account\Account;
use Plexikon\Kernel\Model\Account\Repository\AccountCollection;
use Plexikon\Kernel\Model\Account\Value\AccountId;

final class ChronicleAccountRepository implements AccountCollection
{
    private AggregateRepository $aggregateRepository;

    public function __construct(AggregateRepository $aggregateRepository)
    {
        $this->aggregateRepository = $aggregateRepository;
    }

    public function get(AccountId $accountId): ?Account
    {
        /** @var Account $account */
        $account = $this->aggregateRepository->retrieve($accountId);

        return $account->exists() ? $account : null;
    }

    public function store(Account $customer): void
    {
        $this->aggregateRepository->persist($customer);
    }

    public function getAggregateRepository(): AggregateRepository
    {
        return $this->aggregateRepository;
    }
}
