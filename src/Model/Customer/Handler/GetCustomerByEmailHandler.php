<?php
declare(strict_types=1);

namespace Plexikon\Kernel\Model\Customer\Handler;

use Plexikon\Kernel\Model\Customer\Query\GetCustomerByEmail;
use Plexikon\Kernel\Projection\Customer\CustomerModel;
use React\Promise\Deferred;

final class GetCustomerByEmailHandler
{
    private CustomerModel $model;

    public function __construct(CustomerModel $model)
    {
        $this->model = $model;
    }

    public function query(GetCustomerByEmail $query, Deferred $promise): void
    {
        $customers = $this->model->newQuery()->paginate($query->getEmail()->getValue());

        $promise->resolve($customers);
    }
}
