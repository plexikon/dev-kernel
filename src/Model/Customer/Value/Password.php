<?php
declare(strict_types=1);

namespace Plexikon\Kernel\Model\Customer\Value;

use Plexikon\Kernel\Model\Value;

abstract class Password implements Value
{
    private string $password;

    protected function __construct(string $password)
    {
        $this->password = $password;
    }

    public function getValue()
    {
       return $this->password;
    }

    public function sameValueAs(Value $value): bool
    {
        return $value instanceof $this && $this->password === $value->getValue();
    }
}
