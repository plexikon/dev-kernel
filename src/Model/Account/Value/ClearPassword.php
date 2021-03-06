<?php
declare(strict_types=1);

namespace Plexikon\Kernel\Model\Account\Value;

use Plexikon\Reporter\Exception\Assertion;

class ClearPassword extends Password
{
    const MIN_LENGTH = 10;
    const MAX_LENGTH = 255;

    public static function fromString(string $password): self
    {
        Assertion::betweenLength($password, self::MIN_LENGTH, self::MAX_LENGTH, 'Invalid password');

        return new static($password);
    }
}
