<?php
declare(strict_types=1);

namespace Plexikon\Kernel\Infrastructure\Service;

use Plexikon\Kernel\Model\Customer\Service\UniqueEmailAddress;
use Plexikon\Kernel\Model\Customer\Value\CustomerId;
use Plexikon\Kernel\Model\Customer\Value\EmailAddress;
use Plexikon\Kernel\Projection\Customer\CustomerModel;

final class UniqueEmailFromRead implements UniqueEmailAddress
{
    private CustomerModel $model;

    public function __construct(CustomerModel $model)
    {
        $this->model = $model;
    }

    public function __invoke(EmailAddress $email): ?CustomerId
    {
        /** @var CustomerModel $customer */
        $customer = $this->model->newQuery()->where('email', $email->getValue())->first();

       return $customer ? $customer->getId() : null;
    }
}
