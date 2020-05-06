<?php
declare(strict_types=1);

namespace Plexikon\Kernel\Support\Console;

use Illuminate\Console\Command;
use Plexikon\Chronicle\Support\Contracts\Projector\ProjectorManager;
use Plexikon\Kernel\Model\Customer\Command\RegisterCustomer;
use Plexikon\Reporter\Message\Message;

class CustomersQueryProjection extends Command
{
    protected $signature = 'kernel:query-customers';
    protected ProjectorManager $projectorManager;

    public function __construct(ProjectorManager $projectorManager)
    {
        parent::__construct();

        $this->projectorManager = $projectorManager;
    }

    public function handle(): void
    {
        $projection = $this->projectorManager->createQuery();
        $projection
            ->withQueryFilter($this->projectorManager->projectionQueryFilter())
            ->initialize(['customer_registered' => 0])
            ->whenAny(function (array $state, Message $message): array {
                $event = $message->event();

                if ($event instanceof RegisterCustomer) {
                    $state['customer_registered']++;

                    return $state;
                }
            })
            ->run(false);

        $state = $projection->getState();

        $this->info("{$state['customer_registered']}Customer(s) registered");
    }
}
