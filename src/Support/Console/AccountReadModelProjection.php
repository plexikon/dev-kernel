<?php
declare(strict_types=1);

namespace Plexikon\Kernel\Support\Console;

use Illuminate\Console\Command;
use Plexikon\Chronicle\Support\Contracts\Projector\ProjectorManager;
use Plexikon\Chronicle\Support\Contracts\Projector\ReadModel;
use Plexikon\Kernel\Model\Account\Event\AccountDisabled;
use Plexikon\Kernel\Model\Account\Event\AccountEmailChanged;
use Plexikon\Kernel\Model\Account\Event\AccountEnabled;
use Plexikon\Kernel\Model\Account\Event\AccountNameChanged;
use Plexikon\Kernel\Model\Account\Event\AccountPasswordChanged;
use Plexikon\Kernel\Model\Account\Event\AccountRegistered;
use Plexikon\Kernel\Projection\Customer\AccountReadModel;
use Plexikon\Kernel\Projection\Stream;
use Plexikon\Reporter\Message\Message;

/**
 * @method ReadModel readModel()
 */
class AccountReadModelProjection extends Command
{
    protected $signature = 'kernel:read_model-account';
    protected ProjectorManager $projectorManager;
    protected AccountReadModel $readModel;

    public function __construct(ProjectorManager $projectorManager, AccountReadModel $readModel)
    {
        parent::__construct();

        $this->projectorManager = $projectorManager;
        $this->readModel = $readModel;
    }

    public function handle(): void
    {
        pcntl_async_signals(true);

        $projection = $this->projectorManager->createReadModelProjection(Stream::ACCOUNT, $this->readModel);

        pcntl_signal(SIGINT, function () use ($projection) {
            $projection->stop();
        });

        $projection
            ->withQueryFilter($this->projectorManager->projectionQueryFilter())
            ->initialize(fn() => ['registered' => 0])
            ->fromStreams(Stream::ACCOUNT)
            ->when($this->getAccountHandlers())
            ->run(true);
    }

    protected function getAccountHandlers(): array
    {
        return [
            'account-registered' =>
                function (array $state, AccountRegistered $event): array {
                    $this->readModel()->stack('insert', [
                        'id' => $event->aggregateRootId(),
                        'email' => $event->email()->getValue(),
                        'name' => $event->name()->getValue(),
                        'password' => $event->password()->getValue(),
                        'status' => $event->status()->getValue()
                    ]);

                    $state['registered']++;
                    return $state;
                },

            'account-email-changed' =>
                function (array $state, AccountEmailChanged $event): void {
                    $this->readModel()->stack('update', $event->aggregateRootId(), [
                        'email' => $event->newEmail()->getValue()
                    ]);
                },

            'account-name-changed' => function (array $state, AccountNameChanged $event): void {
                $this->readModel()->stack('update', $event->aggregateRootId(), [
                    'name' => $event->newName()->getValue()
                ]);
            },

            'account-password-changed' =>
                function (array $state, AccountPasswordChanged $event): void {
                    $this->readModel()->stack('update', $event->aggregateRootId(), [
                        'password' => $event->newPassword()->getValue()
                    ]);
                },

            'account-enabled' =>
                function (array $state, AccountEnabled $event): void {
                    $this->readModel()->stack('update', $event->aggregateRootId(), [
                        'status' => $event->newStatus()->getValue()
                    ]);
                },

            'account-disabled' =>
                function (array $state, AccountDisabled $event): void {
                    $this->readModel()->stack('update', $event->aggregateRootId(), [
                        'status' => $event->newStatus()->getValue()
                    ]);
                },
        ];
    }
}
