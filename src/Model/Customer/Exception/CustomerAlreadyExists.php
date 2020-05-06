<?php
declare(strict_types=1);

namespace Plexikon\Kernel\Model\Customer\Exception;

use Plexikon\Kernel\Model\Customer\Value\CustomerId;
use Plexikon\Kernel\Model\Customer\Value\EmailAddress;

final class CustomerAlreadyExists extends CustomerException
{
    public static function withId(CustomerId $customerId): self
    {
        return new self("Customer with id {$customerId->toString()} not found");
    }

    public static function withEmail(EmailAddress $email): self
    {
        return new self("Customer with email {$email->getValue()} not found");
    }
}
