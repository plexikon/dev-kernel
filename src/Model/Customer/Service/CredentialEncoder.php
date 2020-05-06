<?php

namespace Plexikon\Kernel\Model\Customer\Service;

use Plexikon\Kernel\Model\Customer\Value\ClearPassword;
use Plexikon\Kernel\Model\Customer\Value\ClearPasswordConfirmation;
use Plexikon\Kernel\Model\Customer\Value\EncodedPassword;

interface CredentialEncoder
{
    /**
     * @param ClearPasswordConfirmation $passwordConfirmation
     * @return EncodedPassword
     */
    public function encode(ClearPasswordConfirmation $passwordConfirmation): EncodedPassword;

    /**
     * @param ClearPassword $clearPassword
     * @param EncodedPassword $encodedPassword
     * @return bool
     */
    public function check(ClearPassword $clearPassword, EncodedPassword $encodedPassword): bool;

}
