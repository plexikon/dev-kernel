<?php
declare(strict_types=1);

namespace Plexikon\Kernel\Support\Console;

use Illuminate\Console\Command;
use Plexikon\Chronicle\Support\Contracts\Projector\ProjectorManager;
use Plexikon\Chronicle\Support\Contracts\Projector\ReadModel;
use Plexikon\Kernel\Model\Customer\Event\CustomerDisabled;
use Plexikon\Kernel\Model\Customer\Event\CustomerEmailChanged;
use Plexikon\Kernel\Model\Customer\Event\CustomerEnabled;
use Plexikon\Kernel\Model\Customer\Event\CustomerNameChanged;
use Plexikon\Kernel\Model\Customer\Event\CustomerPasswordChanged;
use Plexikon\Kernel\Model\Customer\Event\CustomerRegistered;
use Plexikon\Kernel\Projection\Customer\CustomerReadModel;
use Plexikon\Kernel\Projection\Stream;
use Plexikon\Reporter\Message\Message;

/**
 * @method ReadModel readModel()
 */
class CustomerReadModelProjection extends Command
{
    protected $signature = 'kernel:read_model_customer';
    protected ProjectorManager $projectorManager;
    protected CustomerReadModel $readModel;

    public function __construct(ProjectorManager $projectorManager, CustomerReadModel $readModel)
    {
        parent::__construct();

        $this->projectorManager = $projectorManager;
        $this->readModel = $readModel;
    }

    public function handle(): void
    {
        $projection = $this->projectorManager->createReadModelProjection(Stream::CUSTOMER, $this->readModel);

        $projection
            ->withQueryFilter($this->projectorManager->projectionQueryFilter())
            ->initialize(['registered' => 0])
            ->when($this->getCustomerHandlers())
            ->run(true);
    }

    protected function getCustomerHandlers(): array
    {
        return [
            'customer-registered' =>
                function (array $state, Message $message): array {
                    /** @var CustomerRegistered $event */
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

            'customer-email-changed' =>
                function (array $state, Message $message): void {
                    /** @var CustomerEmailChanged $event */
                    $event = $message->event();

                    $this->readModel()->stack('update', $event->aggregateRootId(), [
                        'email' => $event->newEmail()->getValue()
                    ]);
                },

            'customer-name-changed' => function (array $state, Message $message): void {
                /** @var CustomerNameChanged $event */
                $event = $message->event();
                $this->readModel()->stack('update', $event->aggregateRootId(), [
                    'status' => $event->newName()->getValue()
                ]);
            },

            'customer-password-changed' =>
                function (array $state, Message $message): void {
                    /** @var CustomerPasswordChanged $event */
                    $event = $message->event();
                    $this->readModel()->stack('update', $event->aggregateRootId(), [
                        'password' => $event->newPassword()->getValue()
                    ]);
                },

            'customer-enabled' =>
                function (array $state, Message $message): void {
                    /** @var CustomerEnabled $event */
                    $event = $message->event();
                    $this->readModel()->stack('update', $event->aggregateRootId(), [
                        'status' => $event->newStatus()->getValue()
                    ]);
                },

            'customer-disabled' =>
                function (array $state, Message $message): void {
                    /** @var CustomerDisabled $event */
                    $event = $message->event();
                    $this->readModel()->stack('update', $event->aggregateRootId(), [
                        'status' => $event->newStatus()->getValue()
                    ]);
                },
        ];
    }
}
