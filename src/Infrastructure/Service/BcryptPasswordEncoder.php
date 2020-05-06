<?php
declare(strict_types=1);

namespace Plexikon\Kernel\Infrastructure\Service;

use Illuminate\Contracts\Hashing\Hasher;
use Plexikon\Kernel\Model\Customer\Service\CredentialEncoder;
use Plexikon\Kernel\Model\Customer\Value\ClearPassword;
use Plexikon\Kernel\Model\Customer\Value\ClearPasswordConfirmation;
use Plexikon\Kernel\Model\Customer\Value\EncodedPassword;

final class BcryptPasswordEncoder implements CredentialEncoder
{
    private Hasher $encoder;

    public function __construct(Hasher $encoder)
    {
        $this->encoder = $encoder;
    }

    public function encode(ClearPasswordConfirmation $passwordConfirmation): EncodedPassword
    {
        return EncodedPassword::fromString(
            $this->encoder->make($passwordConfirmation->getValue()),
            PASSWORD_BCRYPT
        );
    }

    public function check(ClearPassword $clearPassword, EncodedPassword $encodedPassword): bool
    {
        return $this->encoder->check($clearPassword->getValue(), $encodedPassword->getValue());
    }
}