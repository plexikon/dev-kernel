<?php
declare(strict_types=1);

namespace Plexikon\Kernel\Model;

use MabeEnum\Enum as BaseEnum;
use MabeEnum\EnumSerializableTrait;
use Serializable;

class Enum extends BaseEnum implements Serializable, Value
{
    use EnumSerializableTrait;

    public function sameValueAs(Value $object): bool
    {
        return $this->is($object);
    }

    public function toString(): string
    {
        return $this->getName();
    }
}
