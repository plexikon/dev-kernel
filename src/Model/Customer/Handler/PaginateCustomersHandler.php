<?php
declare(strict_types=1);

namespace Plexikon\Kernel\Model\Customer\Handler;

use Plexikon\Kernel\Model\Customer\Query\PaginateCustomers;
use Plexikon\Kernel\Projection\Customer\CustomerModel;
use React\Promise\Deferred;

final class PaginateCustomersHandler
{
    private CustomerModel $model;

    public function __construct(CustomerModel $model)
    {
        $this->model = $model;
    }

    public function __invoke(PaginateCustomers $query, Deferred $promise): void
    {
        $customers = $this->model->newQuery()->paginate($query->getLimit());

        $promise->resolve($customers);
    }
}
