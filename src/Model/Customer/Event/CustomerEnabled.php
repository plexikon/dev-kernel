<?php
declare(strict_types=1);

namespace Plexikon\Kernel\Model\Customer\Event;

use Plexikon\Chronicle\Aggregate\AggregateChanged;
use Plexikon\Kernel\Model\Customer\Value\CustomerId;
use Plexikon\Kernel\Model\Customer\Value\CustomerStatus;

final class CustomerEnabled extends AggregateChanged
{
    private ?CustomerStatus $newStatus;
    private ?CustomerStatus $oldStatus;

    public static function forCustomer(CustomerId $customerId, CustomerStatus $newStatus, CustomerStatus $oldStatus): self
    {
        $self = self::occur($customerId->toString(), [
            'new_status' => $newStatus->getValue(),
            'old_status' => $oldStatus->getValue()
        ]);

        $self->newStatus = $newStatus;

        return $self;
    }

    public function newStatus(): CustomerStatus
    {
        return $this->newStatus ?? CustomerStatus::byValue($this->payload['new_status']);
    }

    public function oldStatus(): CustomerStatus
    {
        return $this->oldStatus ?? CustomerStatus::byValue($this->payload['old_status']);
    }
}
