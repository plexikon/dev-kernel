<?php
declare(strict_types=1);

namespace Plexikon\Kernel\Support\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Plexikon\Chronicle\Support\Contracts\Projector\ProjectorManager;
use Plexikon\Chronicle\Support\Projector\ProjectionStatus;
use Plexikon\Kernel\Provider\CustomerServiceProvider;
use Symfony\Component\Process\Process;

class SymfonyWorkerCommand extends Command
{
    protected array $readModels = [
        ...CustomerServiceProvider::READ_MODEL_COMMANDS
    ];

    protected $signature = 'app:read-models';
    protected ProjectorManager $projectorManager;
    protected Collection $processes;

    public function __construct(ProjectorManager $projectorManager)
    {
        parent::__construct();
        $this->projectorManager = $projectorManager;
        $this->processes = new Collection();
    }

    public function handle(): void
    {
        pcntl_async_signals(true);

        foreach ($this->readModels as $streamName => $command) {
            $this->processes->put(
                $streamName,
                Process::fromShellCommandline("php artisan kernel:$command", null, null, null, 0)
            );
        }

        $this->start();

        pcntl_signal(SIGINT, function (): void {
            $this->stopProjection();
        });

//        pcntl_signal(SIGTERM, function (): void {
//            $this->stopProjection();
//        });

        while (true) {
            $this->displayInfo();

            sleep(10);
        }
    }

    protected function stopProjection(): void
    {
        $this->info('Stopping projections ...');

        $idleStatus = ProjectionStatus::IDLE()->getValue();

        foreach ($this->readModels as $streamName => $command) {
            $this->projectorManager->stopProjection($streamName);

//            while ($idleStatus !== $this->projectorManager->statusOf($streamName)) {
//                sleep(1);
//                $this->line($streamName . ' > ' . $this->projectorManager->statusOf($streamName));
//            }

            $this->warn('Projection stopped: ' . $streamName);
        }

        sleep(5);

        $this->stop();

        $this->displayInfo();

        $this->info('All done');

        exit();
    }

    protected function displayInfo(): void
    {
        foreach ($this->displayWhileRunning() as $streamName => $info) {
            $this->line("Stream $streamName - status " . $info['status'] . ' (Pid:' . $info['pid'] . ')');
        }

        $this->line('<----------------->');
    }

    protected function start(): void
    {
        $this->processes->each(function (Process $process): void {
            $process->start();
        });
    }

    protected function stop(): void
    {
        $this->processes->each(function(Process $process, string $streamName){
            $process->stop(0);
        });
    }

    protected function displayWhileRunning(): array
    {
        $display = [];

        $this->processes->each(
            function (Process $process, string $streamName) use (&$display): void {
                $running = $process->isRunning() ? 'Running' : 'Stopped';
                $display += [$streamName => [
                    'status' => $running,
                    'pid' => $process->getPid()
                ]];
            }
        );

        return $display;
    }
}
