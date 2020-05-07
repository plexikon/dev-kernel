<?php
declare(strict_types=1);

namespace Plexikon\Kernel\Model\Account\Value;

use Plexikon\Chronicle\Support\Aggregate\HasAggregateId;
use Plexikon\Chronicle\Support\Contracts\Aggregate\AggregateId;
use Plexikon\Kernel\Model\Value;

final class AccountId implements AggregateId, Value
{
    use HasAggregateId;

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
}
