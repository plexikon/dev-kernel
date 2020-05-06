<?php
declare(strict_types=1);

namespace Plexikon\Kernel\Provider;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use Plexikon\Chronicle\Provider\ChronicleRepositoryManager;
use Plexikon\Chronicle\Provider\ChronicleSnapshotManager;
use Plexikon\Chronicle\Support\Contracts\Snapshot\SnapshotStore;
use Plexikon\Kernel\Infrastructure\Service\BcryptPasswordEncoder;
use Plexikon\Kernel\Infrastructure\Service\UniqueEmailFromRead;
use Plexikon\Kernel\Model\Customer\Handler\CustomerChangeEmailHandler;
use Plexikon\Kernel\Model\Customer\Handler\CustomerChangeNameHandler;
use Plexikon\Kernel\Model\Customer\Handler\CustomerChangePasswordHandler;
use Plexikon\Kernel\Model\Customer\Handler\GetCustomerByEmailHandler;
use Plexikon\Kernel\Model\Customer\Handler\GetCustomerByIdHandler;
use Plexikon\Kernel\Model\Customer\Handler\MarkCustomerAsDisabledHandler;
use Plexikon\Kernel\Model\Customer\Handler\MarkCustomerAsEnabledHandler;
use Plexikon\Kernel\Model\Customer\Handler\PaginateCustomersHandler;
use Plexikon\Kernel\Model\Customer\Handler\RegisterCustomerHandler;
use Plexikon\Kernel\Model\Customer\Repository\CustomerCollection;
use Plexikon\Kernel\Model\Customer\Service\CredentialEncoder;
use Plexikon\Kernel\Model\Customer\Service\UniqueEmailAddress;
use Plexikon\Kernel\Projection\Stream;
use Plexikon\Kernel\Support\Console\CustomerReadModelProjection;
use Plexikon\Kernel\Support\Console\CustomerSnapshotProjection;
use Plexikon\Kernel\Support\Console\CustomersQueryProjection;

class CustomerServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public const COMMAND_MAP = [
        'register-user' => RegisterCustomerHandler::class,
        'mark-customer-as-enabled' => MarkCustomerAsEnabledHandler::class,
        'mark-customer-as-disabled' => MarkCustomerAsDisabledHandler::class,
        'customer-change-email' => CustomerChangeEmailHandler::class,
        'customer-change-name' => CustomerChangeNameHandler::class,
        'customer-change-password' => CustomerChangePasswordHandler::class,
    ];

    public const QUERY_MAP = [
        'paginate-customers' => PaginateCustomersHandler::class,
        'get-customer-by-id' => GetCustomerByIdHandler::class,
        'get-customer-by-email' => GetCustomerByEmailHandler::class,
    ];

    public array $bindings = [
        UniqueEmailAddress::class => UniqueEmailFromRead::class,
        CredentialEncoder::class => BcryptPasswordEncoder::class
    ];

    protected array $consoleCommands = [
        CustomerReadModelProjection::class,
        CustomerSnapshotProjection::class,
        CustomersQueryProjection::class
    ];

    public function register(): void
    {
        $this->app->bindIf(CustomerCollection::class, function (Application $app): CustomerCollection {
            return $app
                ->get(ChronicleRepositoryManager::class)
                ->createRepository(Stream::CUSTOMER);
        }, true);

        $this->app->bindIf(SnapshotStore::class, function (Application $app): SnapshotStore {
            return $app
                ->get(ChronicleSnapshotManager::class)
                ->createSnapshotStore('pgsql');
        }, true);
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands($this->consoleCommands);
        }
    }

    public function provides(): array
    {
        return [CustomerCollection::class, SnapshotStore::class];
    }
}
