<?php
declare(strict_types=1);

namespace Plexikon\Kernel\Model\Customer\Handler;

use Plexikon\Kernel\Model\Customer\Command\CustomerChangeEmail;
use Plexikon\Kernel\Model\Customer\Exception\CustomerAlreadyExists;
use Plexikon\Kernel\Model\Customer\Exception\CustomerNotFound;
use Plexikon\Kernel\Model\Customer\Repository\CustomerCollection;
use Plexikon\Kernel\Model\Customer\Service\UniqueEmailAddress;

final class CustomerChangeEmailHandler
{
    private CustomerCollection $customerCollection;
    private UniqueEmailAddress $uniqueEmail;

    public function __construct(CustomerCollection $customerCollection, UniqueEmailAddress $uniqueEmail)
    {
        $this->customerCollection = $customerCollection;
        $this->uniqueEmail = $uniqueEmail;
    }

    public function command(CustomerChangeEmail $command): void
    {
        $customerId = $command->customerId();

        if (!$customer = $this->customerCollection->get($customerId)) {
            throw CustomerNotFound::withId($customerId);
        }

        $email = $command->newEmail();

        if ($customer->getEmail()->sameValueAs($email)) {
            return;
        }

        if ($customerExists = ($this->uniqueEmail)($email)) {
            throw CustomerAlreadyExists::withEmail($email);
        }

        $customer->changeEmail($email);

        $this->customerCollection->store($customer);
    }
}
