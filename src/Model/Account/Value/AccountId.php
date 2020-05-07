<?php
declare(strict_types=1);

namespace Plexikon\Kernel\Model\Account\Value;

use Plexikon\Chronicle\Support\Aggregate\HasAggregateId;
use Plexikon\Chronicle\Support\Contracts\Aggregate\AggregateId;
use Plexikon\Kernel\Model\Value;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final class AccountId implements AggregateId, Value
{
    use HasAggregateId;

    private UuidInterface $uuid;

    public function __construct(UuidInterface $uuid)
    {
        $this->uuid = $uuid;
    }

    public function sameValueAs(Value $value): bool
    {
        if (!$value instanceof AggregateId) {
            return false;
        }

        return $this->equalsTo($value);
    }

    public function getValue(): string
    {
        return $this->toString();
    }

    public function getUuid(): UuidInterface
    {
        return $this->uuid;
    }
}
