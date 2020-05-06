<?php
declare(strict_types=1);

namespace Plexikon\Kernel\Model\Customer\Command;

use Plexikon\Kernel\Model\Customer\Value\CustomerId;
use Plexikon\Reporter\Command;

final class MarkCustomerAsEnabled extends Command
{
    public function forCustomer(string $customerId): self
    {
        return new self(['customer_id' => $customerId]);
    }

    public function customerId(): CustomerId
    {
        return CustomerId::fromString($this->payload['customer_id']);
    }
}
