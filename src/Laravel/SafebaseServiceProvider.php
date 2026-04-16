<?php

declare(strict_types=1);

namespace LaravelGtm\SafebaseSdk\Laravel;

use Illuminate\Contracts\Cache\Factory as CacheFactory;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Support\ServiceProvider;
use LaravelGtm\SafebaseSdk\SafebaseConnector;
use LaravelGtm\SafebaseSdk\SafebaseSdk;
use Saloon\RateLimitPlugin\Stores\LaravelCacheStore;

class SafebaseServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/safebase-sdk.php', 'safebase-sdk');

        $this->app->singleton(SafebaseConnector::class, function (): SafebaseConnector {
            $configRepository = $this->app->make(ConfigRepository::class);
            $cacheFactory = $this->app->make(CacheFactory::class);
            /** @var array<string, mixed> $config */
            $config = (array) $configRepository->get('safebase-sdk', []);

            return new SafebaseConnector(
                isset($config['base_url']) ? (string) $config['base_url'] : null,
                isset($config['token']) ? (string) $config['token'] : null,
                isset($config['auth_header']) ? (string) $config['auth_header'] : 'X-Api-Key',
                new LaravelCacheStore($cacheFactory->store()),
            );
        });

        $this->app->singleton(SafebaseSdk::class, function (): SafebaseSdk {
            return new SafebaseSdk($this->app->make(SafebaseConnector::class));
        });
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../../config/safebase-sdk.php' => $this->app->configPath('safebase-sdk.php'),
            ], 'safebase-sdk-config');
        }
    }
}
