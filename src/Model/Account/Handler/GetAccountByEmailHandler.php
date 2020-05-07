<?php
declare(strict_types=1);

namespace Plexikon\Kernel\Model\Account\Handler;

use Plexikon\Kernel\Model\Account\Query\GetAccountByEmail;
use Plexikon\Kernel\Projection\Customer\AccountModel;
use React\Promise\Deferred;

final class GetAccountByEmailHandler
{
    private AccountModel $model;

    public function __construct(AccountModel $model)
    {
        $this->model = $model;
    }

    public function query(GetAccountByEmail $query, Deferred $promise): void
    {
        $accounts = $this->model->newQuery()->paginate($query->email()->getValue());

        $promise->resolve($accounts);
    }
}
