<?php
declare(strict_types=1);

namespace Plexikon\Kernel\Model\Customer\Event;

use Plexikon\Chronicle\Aggregate\AggregateChanged;
use Plexikon\Kernel\Model\Customer\Value\CustomerId;
use Plexikon\Kernel\Model\Customer\Value\CustomerStatus;

final class CustomerEnabled extends AggregateChanged
{
    private ?CustomerStatus $status;

    public static function forCustomer(CustomerId $customerId, CustomerStatus $status): self
    {
        $self = self::occur($customerId->toString(), [
            'status' => $status->getValue()
        ]);

        $self->status = $status;

        return $self;
    }

    public function status(): CustomerStatus
    {
        return $this->status ?? CustomerStatus::byValue($this->payload['status']);
    }
}
