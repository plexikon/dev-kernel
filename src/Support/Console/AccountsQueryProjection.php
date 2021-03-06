<?php
declare(strict_types=1);

namespace Plexikon\Kernel\Support\Console;

use Illuminate\Console\Command;
use Plexikon\Chronicle\Aggregate\AggregateChanged;
use Plexikon\Chronicle\Support\Contracts\Projector\ProjectorManager;
use Plexikon\Kernel\Model\Account\Command\RegisterAccount;
use Plexikon\Kernel\Model\Account\Event\AccountRegistered;
use Plexikon\Kernel\Projection\Stream;
use Plexikon\Reporter\Message\Message;

class AccountsQueryProjection extends Command
{
    protected $signature = 'kernel:query-accounts';
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
            ->initialize(fn() => ['registered' => 0])
            ->fromStreams(Stream::ACCOUNT)
            ->whenAny(function (array $state, AggregateChanged $event): array {
                if ($event instanceof AccountRegistered) {
                    $state['registered']++;
                }

                return $state;
            })
            ->run(false);

        $state = $projection->getState();

        $this->info("{$state['registered']}Account(s) registered");
    }
}
