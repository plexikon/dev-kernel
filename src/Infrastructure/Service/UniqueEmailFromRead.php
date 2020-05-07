<?php
declare(strict_types=1);

namespace Plexikon\Kernel\Infrastructure\Service;

use Plexikon\Kernel\Model\Account\Service\UniqueEmailAddress;
use Plexikon\Kernel\Model\Account\Value\AccountId;
use Plexikon\Kernel\Model\Account\Value\EmailAddress;
use Plexikon\Kernel\Projection\Customer\AccountModel;

final class UniqueEmailFromRead implements UniqueEmailAddress
{
    private AccountModel $model;

    public function __construct(AccountModel $model)
    {
        $this->model = $model;
    }

    public function __invoke(EmailAddress $email): ?AccountId
    {
        /** @var AccountModel $customer */
        $customer = $this->model->newQuery()->where('email', $email->getValue())->first();

       return $customer ? $customer->getId() : null;
    }
}
