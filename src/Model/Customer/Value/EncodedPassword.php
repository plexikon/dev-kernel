<?php
declare(strict_types=1);

namespace Plexikon\Kernel\Model\Customer\Value;

use Plexikon\Reporter\Exception\Assertion;

final class EncodedPassword extends Password
{
    private string $hash;

    public static function fromString(string $encodedPassword, string $hash)
    {
        Assertion::same(password_get_info($encodedPassword)['algo'], $hash);

        $self = new static($encodedPassword);
        $self->hash = $hash;

        return $self;
    }

    public function getHash(): string
    {
        return $this->hash;
    }
}
