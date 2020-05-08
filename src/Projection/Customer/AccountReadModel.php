<?php
declare(strict_types=1);

namespace Plexikon\Kernel\Projection\Customer;

use Illuminate\Database\Schema\Blueprint;
use Plexikon\Chronicle\Support\ReadModel\ConnectionReadModel;
use Plexikon\Chronicle\Support\ReadModel\HasConnectionOperation;
use Plexikon\Kernel\Model\Account\Value\AccountStatus;
use Plexikon\Kernel\Projection\Table;

final class AccountReadModel extends ConnectionReadModel
{
    use HasConnectionOperation;

    protected function up(): callable
    {
        return function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('email')->unique();
            $table->string('name');
            $table->string('password');
            $table->enum('status', AccountStatus::getValues());
        };
    }

    protected function tableName(): string
    {
        return Table::ACCOUNT;
    }
}
