<?php
declare(strict_types=1);

namespace Plexikon\Kernel\Model\Customer\Value;

use Plexikon\Kernel\Model\Value;
use Plexikon\Reporter\Exception\Assertion;

final class EmailAddress implements Value
{
    private string $email;

    public function __construct(string $email)
    {
        $this->email = $email;
    }

    public static function fromString(string $email): self
    {
        Assertion::email($email, 'Email is invalid');

        return new self($email);
    }

    public function sameValueAs(Value $value): bool
    {
        return $value instanceof $this && $this->email == $value->getValue();
    }

    public function getValue(): string
    {
        return $this->email;
    }
}
