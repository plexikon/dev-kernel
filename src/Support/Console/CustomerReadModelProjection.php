<?php
declare(strict_types=1);

namespace Plexikon\Kernel\Support\Console;

use Illuminate\Console\Command;
use Plexikon\Chronicle\Support\Contracts\Projector\ProjectorManager;
use Plexikon\Chronicle\Support\Contracts\Projector\ReadModel;
use Plexikon\Kernel\Model\Customer\Event\CustomerDisabled;
use Plexikon\Kernel\Model\Customer\Event\CustomerEmailChanged;
use Plexikon\Kernel\Model\Customer\Event\CustomerEnabled;
use Plexikon\Kernel\Model\Customer\Event\CustomerPasswordChanged;
use Plexikon\Kernel\Model\Customer\Event\CustomerRegistered;
use Plexikon\Kernel\Projection\Customer\CustomerReadModel;
use Plexikon\Kernel\Projection\Stream;
use Plexikon\Reporter\Message\Message;

/**
 * @method ReadModel readModel()
 */
final class CustomerReadModelProjection extends Command
{
    protected $signature = 'kernel:read-model_customer';

    private ProjectorManager $projectorManager;
    private CustomerReadModel $readModel;

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
            ->when(
                [
                    'customer-registered' =>
                        function (array $state, Message $message): array {
                            /** @var CustomerRegistered $event */
                            $event = $message->event();

                            $this->readModel()->stack('insert', [
                                'id' => $event->aggregateRootId(),
                                'email' => $event->getEmail()->getValue(),
                                'password' => $event->getPassword()->getValue(),
                                'status' => $event->getStatus()->getValue()
                            ]);

                            $state['registered']++;
                            return $state;
                        },

                    'customer-email-changed' =>
                        function (array $state, Message $message): void {
                            /** @var CustomerEmailChanged $event */
                            $event = $message->event();

                            $this->readModel()->stack('update', $event->aggregateRootId(), [
                                'email' => $event->getNewEmail()->getValue()
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
                                'status' => $event->status()->getValue()
                            ]);
                        },

                    'customer-disabled' =>
                        function (array $state, Message $message): void {
                            /** @var CustomerDisabled $event */
                            $event = $message->event();
                            $this->readModel()->stack('update', $event->aggregateRootId(), [
                                'status' => $event->status()->getValue()
                            ]);
                        },
                ])
            ->run(true);
    }
}
