<?php

namespace Plexikon\Kernel\Model\Account\Service;

use Plexikon\Kernel\Model\Account\Value\ClearPassword;
use Plexikon\Kernel\Model\Account\Value\ClearPasswordConfirmation;
use Plexikon\Kernel\Model\Account\Value\BcryptEncodedPassword;

interface CredentialEncoder
{
    /**
     * @param ClearPasswordConfirmation $passwordConfirmation
     * @return BcryptEncodedPassword
     */
    public function encode(ClearPasswordConfirmation $passwordConfirmation): BcryptEncodedPassword;

    /**
     * @param ClearPassword $clearPassword
     * @param BcryptEncodedPassword $encodedPassword
     * @return bool
     */
    public function check(ClearPassword $clearPassword, BcryptEncodedPassword $encodedPassword): bool;

}
