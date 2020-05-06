<?php

namespace Plexikon\Kernel\Model\Customer\Service;

use Plexikon\Kernel\Model\Customer\Value\ClearPassword;
use Plexikon\Kernel\Model\Customer\Value\ClearPasswordConfirmation;
use Plexikon\Kernel\Model\Customer\Value\BcryptEncodedPassword;

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
