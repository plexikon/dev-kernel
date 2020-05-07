<?php
declare(strict_types=1);

namespace Plexikon\Kernel\Model\Account\Handler;

use Plexikon\Kernel\Model\Account\Query\PaginateAccounts;
use Plexikon\Kernel\Projection\Customer\AccountModel;
use React\Promise\Deferred;

final class PaginateAccountsHandler
{
    private AccountModel $model;

    public function __construct(AccountModel $model)
    {
        $this->model = $model;
    }

    public function query(PaginateAccounts $query, Deferred $promise): void
    {
        $customers = $this->model->newQuery()->paginate($query->limit());

        $promise->resolve($customers);
    }
}
