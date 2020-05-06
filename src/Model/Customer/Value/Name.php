<?php
declare(strict_types=1);

namespace Plexikon\Kernel\Model\Customer\Value;

use Assert\Assert;
use Plexikon\Kernel\Model\Value;
use function get_class;

class Name implements Value
{
    public const MIN_LENGTH = 4;

    private string $name;

    protected function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function fromString(string $name): self
    {
        Assert::that($name, 'Invalid name')
            ->notBlank()
            ->minLength(self::MIN_LENGTH);

        return new self($name);
    }

    public function sameValueAs(Value $value): bool
    {
        return static::class === get_class($value)
            && $this->getValue() === $value->getValue();
    }

    public function getValue(): string
    {
        return $this->name;
    }
}
