<?php
declare(strict_types=1);

namespace Plexikon\Kernel\Model\Account\Handler;

use Plexikon\Kernel\Model\Account\Query\GetAccountById;
use Plexikon\Kernel\Projection\Customer\AccountModel;
use React\Promise\Deferred;

final class GetAccountByIdHandler
{
    private AccountModel $model;

    public function __construct(AccountModel $model)
    {
        $this->model = $model;
    }

    public function query(GetAccountById $query, Deferred $promise): void
    {
        $accounts = $this->model->newQuery()->find($query->accountId()->getValue());

        $promise->resolve($accounts);
    }
}
