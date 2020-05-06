<?php
declare(strict_types=1);

namespace Plexikon\Kernel\Model\Customer\Handler;

use Plexikon\Kernel\Model\Customer\Command\RegisterCustomer;
use Plexikon\Kernel\Model\Customer\Customer;
use Plexikon\Kernel\Model\Customer\Exception\CustomerAlreadyExists;
use Plexikon\Kernel\Model\Customer\Repository\CustomerCollection;
use Plexikon\Kernel\Model\Customer\Service\CredentialEncoder;
use Plexikon\Kernel\Model\Customer\Service\UniqueEmailAddress;

final class RegisterCustomerHandler
{
    private CustomerCollection $customerCollection;
    private UniqueEmailAddress $uniqueEmail;
    private CredentialEncoder $credentialEncoder;

    public function __construct(CustomerCollection $customerCollection,
                                UniqueEmailAddress $uniqueEmail,
                                CredentialEncoder $credentialEncoder)
    {
        $this->customerCollection = $customerCollection;
        $this->uniqueEmail = $uniqueEmail;
        $this->credentialEncoder = $credentialEncoder;
    }

    public function command(RegisterCustomer $command): void
    {
        $customerId = $command->customerId();

        if ($customer = $this->customerCollection->get($customerId)) {
            throw CustomerAlreadyExists::withId($customerId);
        }

        $email = $command->email();

        if ($customerExists = ($this->uniqueEmail)($email)) {
            throw CustomerAlreadyExists::withEmail($email);
        }

        $encodedPassword = $this->credentialEncoder->encode($command->clearPassword());

        $customer = Customer::register($customerId, $email, $command->name(), $encodedPassword);

        $this->customerCollection->store($customer);
    }
}
