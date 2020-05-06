<?php
declare(strict_types=1);

namespace Plexikon\Kernel\Model\Customer\Command;

use Plexikon\Kernel\Model\Customer\Value\CustomerId;
use Plexikon\Kernel\Model\Customer\Value\Name;
use Plexikon\Reporter\Command;

final class CustomerChangeName extends Command
{
    public function forCustomer(string $customerId, string $newName)
    {
        return new self([
            'customer_id' => $customerId,
            'new_name' => $newName
        ]);
    }

    public function customerId(): CustomerId
    {
        return CustomerId::fromString($this->payload['customer_id']);
    }

    public function newName(): Name
    {
        return Name::fromString($this->payload['name']);
    }
}
