<?php
declare(strict_types=1);

namespace Plexikon\Kernel\Projection\Customer;

use Illuminate\Database\Eloquent\Model;
use Plexikon\Kernel\Model\Customer\Value\BcryptEncodedPassword;
use Plexikon\Kernel\Model\Customer\Value\CustomerId;
use Plexikon\Kernel\Model\Customer\Value\CustomerStatus;
use Plexikon\Kernel\Model\Customer\Value\EmailAddress;
use Plexikon\Kernel\Projection\Table;

class CustomerModel extends Model
{
    protected $table = Table::CUSTOMER;
    protected $guarded = ['*'];
    protected $hidden = ['password'];
    protected $keyType = 'string';
    public $incrementing = false;

    public function getId(): CustomerId
    {
        return CustomerId::fromString($this->getKey());
    }

    public function getEmail(): EmailAddress
    {
        return EmailAddress::fromString($this['email']);
    }

    public function getPassword(): BcryptEncodedPassword
    {
        return BcryptEncodedPassword::fromString($this['password']);
    }

    public function getStatus(): CustomerStatus
    {
        return CustomerStatus::byValue($this['status']);
    }
}
