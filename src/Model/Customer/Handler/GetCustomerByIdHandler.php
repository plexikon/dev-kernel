<?php
declare(strict_types=1);

namespace Plexikon\Kernel\Model\Customer\Handler;

use Plexikon\Kernel\Model\Customer\Query\GetCustomerById;
use Plexikon\Kernel\Projection\Customer\CustomerModel;
use React\Promise\Deferred;

final class GetCustomerByIdHandler
{
    private CustomerModel $model;

    public function __construct(CustomerModel $model)
    {
        $this->model = $model;
    }

    public function query(GetCustomerById $query, Deferred $promise): void
    {
        $customers = $this->model->newQuery()->find($query->getCustomerId()->getValue());

        $promise->resolve($customers);
    }
}
