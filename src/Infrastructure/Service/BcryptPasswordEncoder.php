<?php
declare(strict_types=1);

namespace Plexikon\Kernel\Infrastructure\Service;

use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Hashing\BcryptHasher;
use Plexikon\Kernel\Exception\InvalidArgumentException;
use Plexikon\Kernel\Model\Account\Service\CredentialEncoder;
use Plexikon\Kernel\Model\Account\Value\BcryptEncodedPassword;
use Plexikon\Kernel\Model\Account\Value\ClearPassword;
use Plexikon\Kernel\Model\Account\Value\ClearPasswordConfirmation;
use function get_class;

final class BcryptPasswordEncoder implements CredentialEncoder
{
    private Hasher $encoder;

    public function __construct(Hasher $encoder)
    {
        if (!$encoder instanceof BcryptHasher) {
            $message = 'Hasher must be an instance of ' . (BcryptHasher::class) . ' got: ' . get_class($encoder);

            throw new InvalidArgumentException($message);
        }

        $this->encoder = $encoder;
    }

    public function encode(ClearPasswordConfirmation $passwordConfirmation): BcryptEncodedPassword
    {
        return BcryptEncodedPassword::fromString(
            $this->encoder->make($passwordConfirmation->getValue())
        );
    }

    public function check(ClearPassword $clearPassword, BcryptEncodedPassword $encodedPassword): bool
    {
        return $this->encoder->check($clearPassword->getValue(), $encodedPassword->getValue());
    }
}
