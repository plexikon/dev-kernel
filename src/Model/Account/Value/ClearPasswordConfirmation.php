<?php
declare(strict_types=1);

namespace Plexikon\Kernel\Model\Account\Value;

use Plexikon\Reporter\Exception\Assertion;

final class ClearPasswordConfirmation extends ClearPassword
{
    public static function withConfirmation(string $password, string $passwordConfirmation): self
    {
        Assertion::same($password, $passwordConfirmation, 'Invalid password');

        return self::fromString($password);
    }
}
