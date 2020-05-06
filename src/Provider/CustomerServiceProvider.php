<?php
declare(strict_types=1);

namespace Plexikon\Kernel\Provider;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use Plexikon\Chronicle\Provider\ChronicleRepositoryManager;
use Plexikon\Kernel\Infrastructure\Service\BcryptPasswordEncoder;
use Plexikon\Kernel\Infrastructure\Service\UniqueEmailFromRead;
use Plexikon\Kernel\Model\Customer\Repository\CustomerCollection;
use Plexikon\Kernel\Model\Customer\Service\CredentialEncoder;
use Plexikon\Kernel\Model\Customer\Service\UniqueEmailAddress;
use Plexikon\Kernel\Projection\Stream;

class CustomerServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public array $bindings = [
        UniqueEmailAddress::class => UniqueEmailFromRead::class,
        CredentialEncoder::class => BcryptPasswordEncoder::class
    ];


    public function register(): void
    {
        $this->app->singleton(CustomerCollection::class, function (Application $app): CustomerCollection {
            return $app
                ->get(ChronicleRepositoryManager::class)
                ->createRepository(Stream::CUSTOMER);
        });
    }

    public function provides(): array
    {
        return [CustomerCollection::class];
    }
}
