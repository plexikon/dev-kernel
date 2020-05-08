<?php
declare(strict_types=1);

namespace Plexikon\Kernel\Provider;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Plexikon\Chronicle\Provider\ChronicleServiceProvider;
use Plexikon\Chronicle\Provider\ChronicleStoreManager;
use Plexikon\Chronicle\Provider\ProjectorServiceManager;
use Plexikon\Chronicle\Support\Contracts\Chronicler\Chronicle;
use Plexikon\Chronicle\Support\Contracts\Projector\ProjectorManager;
use Plexikon\Reporter\CommandPublisher;
use Plexikon\Reporter\Contracts\Publisher\Publisher;
use Plexikon\Reporter\EventPublisher;
use Plexikon\Reporter\Manager\ReporterDriverManager;
use Plexikon\Reporter\Manager\ReporterServiceProvider;
use Plexikon\Reporter\QueryPublisher;

class AppServiceProvider extends ServiceProvider
{
    public const DEFAULT_PERSISTENCE = 'pgsql';

    public function register()
    {
        $this->app->register(ReporterServiceProvider::class);

        $this->app->register(ChronicleServiceProvider::class);

        $this->registerDefaultPublishers();

        $this->app->bindIf(Chronicle::class, function (Application $app): Chronicle {
            return $app->get(ChronicleStoreManager::class)->createChronicleStore(self::DEFAULT_PERSISTENCE);
        }, true);

        $this->app->bindIf(ProjectorManager::class, function (Application $app): ProjectorManager {
            return $app->get(ProjectorServiceManager::class)->createProjectorManager();
        }, true);
    }

    private function registerDefaultPublishers(): void
    {
        $this->app->bindIf(CommandPublisher::class, function (Application $app): Publisher {
            return $app->get(ReporterDriverManager::class)->commandPublisher();
        }, true);

        $this->app->bindIf(QueryPublisher::class, function (Application $app): Publisher {
            return $app->get(ReporterDriverManager::class)->queryPublisher();
        });

        $this->app->bindIf(EventPublisher::class, function (Application $app): Publisher {
            return $app->get(ReporterDriverManager::class)->eventPublisher();
        }, true);
    }

    public function boot()
    {
        //
    }
}
