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
    protected string $defaultPersistence = 'pgsql';

    public function register()
    {
        $this->app->register(ReporterServiceProvider::class);

        $this->app->register(ChronicleServiceProvider::class);

        $this->registerDefaultPublishers();

        $this->app->singleton(Chronicle::class, function (Application $app): Chronicle {
            return $app->get(ChronicleStoreManager::class)->createChronicleStore($this->defaultPersistence);
        });

        $this->app->singleton(ProjectorManager::class, function (Application $app): ProjectorManager {
            return $app->get(ProjectorServiceManager::class)->createProjectorManager();
        });
    }

    private function registerDefaultPublishers(): void
    {
        $this->app->singleton(CommandPublisher::class, function (Application $app): Publisher {
            return $app->get(ReporterDriverManager::class)->commandPublisher();
        });

        $this->app->singleton(QueryPublisher::class, function (Application $app): Publisher {
            return $app->get(ReporterDriverManager::class)->queryPublisher();
        });

        $this->app->singleton(EventPublisher::class, function (Application $app): Publisher {
            return $app->get(ReporterDriverManager::class)->eventPublisher();
        });
    }

    public function boot()
    {
        //
    }
}
