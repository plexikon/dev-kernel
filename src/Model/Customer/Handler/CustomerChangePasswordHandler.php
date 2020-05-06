<?php
declare(strict_types=1);

namespace Plexikon\Kernel\Model\Customer\Handler;

use Plexikon\Kernel\Model\Customer\Command\CustomerChangePassword;
use Plexikon\Kernel\Model\Customer\Exception\BadCredentials;
use Plexikon\Kernel\Model\Customer\Exception\CustomerNotFound;
use Plexikon\Kernel\Model\Customer\Repository\CustomerCollection;
use Plexikon\Kernel\Model\Customer\Service\CredentialEncoder;
use Plexikon\Kernel\Model\Customer\Value\BcryptEncodedPassword;

final class CustomerChangePasswordHandler
{
    private CustomerCollection $customerCollection;
    private CredentialEncoder $credentialEncoder;

    public function __construct(CustomerCollection $customerCollection, CredentialEncoder $credentialEncoder)
    {
        $this->customerCollection = $customerCollection;
        $this->credentialEncoder = $credentialEncoder;
    }

    public function command(CustomerChangePassword $command): void
    {
        $customerId = $command->customerId();

        if (!$customer = $this->customerCollection->get($customerId)) {
            throw CustomerNotFound::withId($customerId);
        }

        $encodedPassword = $this->encodeValidatedPassword($command, $customer->getPassword());

        $customer->changePassword($encodedPassword);

        $this->customerCollection->store($customer);
    }

    private function encodeValidatedPassword(CustomerChangePassword $command, BcryptEncodedPassword $password): BcryptEncodedPassword
    {
        if (!$this->credentialEncoder->check($command->currentClearPassword(), $password)) {
            throw new BadCredentials('Invalid password');
        }

        return $this->credentialEncoder->encode($command->newClearPasswordConfirmation());
    }
}
