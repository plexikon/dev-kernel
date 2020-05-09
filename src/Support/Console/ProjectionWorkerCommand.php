<?php
declare(strict_types=1);

namespace Plexikon\Kernel\Support\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Plexikon\Chronicle\Support\Contracts\Projector\ProjectorManager;
use Plexikon\Chronicle\Support\Projector\ProjectionStatus;
use Symfony\Component\Process\Process;

class ProjectionWorkerCommand extends Command
{
    protected array $projections = [];

    protected $signature = 'kernel:projections';
    protected string $baseCommand = 'exec php artisan kernel:';
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

        pcntl_signal(SIGINT, function (int $signal): void {
            $this->stop($signal);
            exit();
        });

        foreach ($this->projections as $streamName => $command) {
            $this->processes->put(
                $streamName,
                $this->createProcess($this->baseCommand . $command)
            );
        }

        $this->start();

        while (true) {
            $this->displayInfo();
            sleep(5);
        }
    }

    protected function start(): void
    {
        $this->processes->each(function (Process $process): void {
            $process->start();
        });
    }

    protected function stop(int $signal): void
    {
        $this->processes->each(function (Process $process, string $streamName) use ($signal): void {
            $idleStatus = ProjectionStatus::IDLE;
            $round = 10;

            while ($idleStatus !== $this->projectorManager->statusOf($streamName)) {
                sleep(1);

                if ($round === 0) {
                    $this->error('Unable to stop gracefully ' . $streamName);
                    $idleStatus = ProjectionStatus::IDLE;
                }

                $round--;
            }

            $process->stop(0, $signal);

            $this->line($streamName . ' stopped');
        });
    }

    protected function displayInfo(): void
    {
        $this->processes->each(function (Process $process, string $streamName): void {
            $isRunning = $process->isRunning() ? 'running' : $process->getStatus();
            $this->line($streamName . ' is ' . $isRunning . ' with Pid ' . $process->getPid());
        });
    }

    protected function createProcess(string $command)
    {
        return Process::fromShellCommandline($command, base_path())
            ->setTimeout(null)
            ->disableOutput();
    }
}
