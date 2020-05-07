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
        $projection = $this->projectorManager->createReadModelProjection(Stream::ACCOUNT, $this->readModel);

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
                function (array $state, Message $message): array {
                    /** @var AccountRegistered $event */
                    $event = $message->event();

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
                function (array $state, Message $message): void {
                    /** @var AccountEmailChanged $event */
                    $event = $message->event();

                    $this->readModel()->stack('update', $event->aggregateRootId(), [
                        'email' => $event->newEmail()->getValue()
                    ]);
                },

            'account-name-changed' => function (array $state, Message $message): void {
                /** @var AccountNameChanged $event */
                $event = $message->event();
                $this->readModel()->stack('update', $event->aggregateRootId(), [
                    'name' => $event->newName()->getValue()
                ]);
            },

            'account-password-changed' =>
                function (array $state, Message $message): void {
                    /** @var AccountPasswordChanged $event */
                    $event = $message->event();
                    $this->readModel()->stack('update', $event->aggregateRootId(), [
                        'password' => $event->newPassword()->getValue()
                    ]);
                },

            'account-enabled' =>
                function (array $state, Message $message): void {
                    /** @var AccountEnabled $event */
                    $event = $message->event();
                    $this->readModel()->stack('update', $event->aggregateRootId(), [
                        'status' => $event->newStatus()->getValue()
                    ]);
                },

            'account-disabled' =>
                function (array $state, Message $message): void {
                    /** @var AccountDisabled $event */
                    $event = $message->event();
                    $this->readModel()->stack('update', $event->aggregateRootId(), [
                        'status' => $event->newStatus()->getValue()
                    ]);
                },
        ];
    }
}
