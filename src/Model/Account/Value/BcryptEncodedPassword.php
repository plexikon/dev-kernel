<?php
declare(strict_types=1);

namespace Plexikon\Kernel\Model\Account\Value;

use Plexikon\Reporter\Exception\Assertion;

final class BcryptEncodedPassword extends Password
{
    public static function fromString(string $encodedPassword): self
    {
        Assertion::same(password_get_info($encodedPassword)['algo'], PASSWORD_BCRYPT);

        return new static($encodedPassword);
    }
}
