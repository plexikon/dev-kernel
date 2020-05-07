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
use Plexikon\Kernel\Model\Account\Handler\AccountChangeEmailHandler;
use Plexikon\Kernel\Model\Account\Handler\AccountChangeNameHandler;
use Plexikon\Kernel\Model\Account\Handler\AccountChangePasswordHandler;
use Plexikon\Kernel\Model\Account\Handler\GetAccountByEmailHandler;
use Plexikon\Kernel\Model\Account\Handler\GetAccountByIdHandler;
use Plexikon\Kernel\Model\Account\Handler\MarkAccountAsDisabledHandler;
use Plexikon\Kernel\Model\Account\Handler\MarkAccountAsEnabledHandler;
use Plexikon\Kernel\Model\Account\Handler\PaginateAccountsHandler;
use Plexikon\Kernel\Model\Account\Handler\RegisterAccountHandler;
use Plexikon\Kernel\Model\Account\Repository\AccountCollection;
use Plexikon\Kernel\Model\Account\Service\CredentialEncoder;
use Plexikon\Kernel\Model\Account\Service\UniqueEmailAddress;
use Plexikon\Kernel\Projection\Stream;
use Plexikon\Kernel\Support\Console\AccountReadModelProjection;
use Plexikon\Kernel\Support\Console\AccountSnapshotProjection;
use Plexikon\Kernel\Support\Console\AccountsQueryProjection;
use Plexikon\Kernel\Support\Console\SymfonyWorkerCommand;

class AccountServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public const COMMAND_MAP = [
        'register-account' => RegisterAccountHandler::class,
        'mark-account-as-enabled' => MarkAccountAsEnabledHandler::class,
        'mark-account-as-disabled' => MarkAccountAsDisabledHandler::class,
        'account-change-email' => AccountChangeEmailHandler::class,
        'account-change-name' => AccountChangeNameHandler::class,
        'account-change-password' => AccountChangePasswordHandler::class,
    ];

    public const QUERY_MAP = [
        'paginate-accounts' => PaginateAccountsHandler::class,
        'get-account-by-id' => GetAccountByIdHandler::class,
        'get-account-by-email' => GetAccountByEmailHandler::class,
    ];

    public const READ_MODEL_COMMANDS = [
        'read_model-account',
        'snapshot-account'
    ];

    public array $bindings = [
        UniqueEmailAddress::class => UniqueEmailFromRead::class,
        CredentialEncoder::class => BcryptPasswordEncoder::class
    ];

    protected array $consoleCommands = [
        AccountReadModelProjection::class,
        AccountSnapshotProjection::class,
        AccountsQueryProjection::class,
        SymfonyWorkerCommand::class,
    ];

    public function register(): void
    {
        $this->app->bindIf(AccountCollection::class, function (Application $app): AccountCollection {
            return $app
                ->get(ChronicleRepositoryManager::class)
                ->createRepository(Stream::ACCOUNT);
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
        return [AccountCollection::class, SnapshotStore::class];
    }
}
