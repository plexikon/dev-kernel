<?php
declare(strict_types=1);

namespace Plexikon\Kernel\Model\Customer\Event;

use Plexikon\Chronicle\Aggregate\AggregateChanged;
use Plexikon\Kernel\Model\Customer\Value\CustomerId;
use Plexikon\Kernel\Model\Customer\Value\Name;

final class CustomerNameChanged extends AggregateChanged
{
    private ?Name $newName;
    private ?Name $oldName;

    public static function forCustomer(CustomerId $customerId, Name $newName, Name $oldName)
    {
        $self = self::occur($customerId->getValue(),[
            'customer_id' => $customerId,
            'new_name' => $newName
        ]);

        $self->newName = $newName;
        $self->oldName = $oldName;

        return $self;
    }

    public function customerId(): CustomerId
    {
        return CustomerId::fromString($this->aggregateRootId());
    }

    public function newName(): Name
    {
        return Name::fromString($this->payload['new_name']);
    }

    public function oldName(): Name
    {
        return Name::fromString($this->payload['old_name']);
    }
}
