<?php
declare(strict_types=1);

namespace Plexikon\Kernel\Projection\Customer;

use Illuminate\Database\Schema\Blueprint;
use Plexikon\Chronicle\Support\ReadModel\ConnectionReadModel;
use Plexikon\Chronicle\Support\ReadModel\HasConnectionOperation;
use Plexikon\Kernel\Model\Customer\Value\CustomerStatus;
use Plexikon\Kernel\Projection\Table;

final class CustomerReadModel extends ConnectionReadModel
{
    use HasConnectionOperation;

    protected function up(): callable
    {
        return function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('status', CustomerStatus::getValues());
        };
    }

    protected function tableName(): string
    {
        return Table::CUSTOMER;
    }
}
