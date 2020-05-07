<?php
declare(strict_types=1);

namespace Plexikon\Kernel\Projection\Customer;

use Illuminate\Database\Eloquent\Model;
use Plexikon\Kernel\Model\Account\Value\BcryptEncodedPassword;
use Plexikon\Kernel\Model\Account\Value\AccountId;
use Plexikon\Kernel\Model\Account\Value\AccountStatus;
use Plexikon\Kernel\Model\Account\Value\EmailAddress;
use Plexikon\Kernel\Model\Account\Value\Name;
use Plexikon\Kernel\Projection\Table;

class AccountModel extends Model
{
    protected $table = Table::CUSTOMER;
    protected $guarded = ['*'];
    protected $hidden = ['password'];
    protected $keyType = 'string';
    public $incrementing = false;

    public function getId(): AccountId
    {
        return AccountId::fromString($this->getKey());
    }

    public function getEmail(): EmailAddress
    {
        return EmailAddress::fromString($this['email']);
    }

    public function getCustomerName(): Name
    {
        return Name::fromString($this['name']);
    }

    public function getPassword(): BcryptEncodedPassword
    {
        return BcryptEncodedPassword::fromString($this['password']);
    }

    public function getStatus(): AccountStatus
    {
        return AccountStatus::byValue($this['status']);
    }
}
