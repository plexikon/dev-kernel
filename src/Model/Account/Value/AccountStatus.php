<?php
declare(strict_types=1);

namespace Plexikon\Kernel\Model\Account\Value;

use Plexikon\Kernel\Model\Enum;

/**
 * @method static AccountStatus REGISTERED()
 * @method static AccountStatus ENABLED()
 * @method static AccountStatus DISABLED()
 */
final class AccountStatus extends Enum
{
    public const REGISTERED = 'registered';
    public const ENABLED = 'enabled';
    public const DISABLED = 'disabled';
}
