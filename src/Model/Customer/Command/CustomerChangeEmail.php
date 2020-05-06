<?php
declare(strict_types=1);

namespace Plexikon\Kernel\Model\Customer\Command;

use Plexikon\Kernel\Model\Customer\Value\CustomerId;
use Plexikon\Kernel\Model\Customer\Value\EmailAddress;
use Plexikon\Reporter\Command;

final class CustomerChangeEmail extends Command
{
    public static function forCustomer(string $customerId, string $newEmail): self
    {
        return new self([
            'customer_id' => $customerId,
            'new_email' => $newEmail
        ]);
    }

    public function customerId(): CustomerId
    {
        return CustomerId::fromString($this->payload['customer_id']);
    }

    public function newEmail(): EmailAddress
    {
        return EmailAddress::fromString($this->payload['new_email']);
    }
}
