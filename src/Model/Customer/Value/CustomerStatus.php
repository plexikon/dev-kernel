<?php
declare(strict_types=1);

namespace Plexikon\Kernel\Model\Customer\Value;

use Plexikon\Kernel\Model\Enum;

/**
 * @method static CustomerStatus REGISTERED()
 * @method static CustomerStatus ENABLED()
 * @method static CustomerStatus DISABLED()
 */
final class CustomerStatus extends Enum
{
    public const REGISTERED = 'registered';
    public const ENABLED = 'enabled';
    public const DISABLED = 'disabled';
}
