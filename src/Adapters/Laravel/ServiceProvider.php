<?php
declare(strict_types=1);

namespace Laque\Identity\Adapters\Laravel;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Laque\Identity\Contracts\IdentityProviderInterface;
use Laque\Identity\Providers\MockProvider;
use Laque\Identity\Providers\CsvProvider;
use Laque\Identity\Providers\NidaProvider;
use Laque\Identity\Contracts\TransportInterface;

final class ServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/config/identity.php', 'identity');

        $this->app->bind(IdentityProviderInterface::class, function ($app) {
            $cfg = $app['config']->get('identity');
            $driver = $cfg['driver'] ?? 'mock';
            return match ($driver) {
                'mock' => new MockProvider(),
                'csv'  => new CsvProvider($cfg['csv_path'] ?? base_path('storage/identity.csv')),
                'nida' => new NidaProvider(
                    $app->make(TransportInterface::class),
                    $cfg['nida']['endpoint'] ?? '',
                    $cfg['nida']['api_key'] ?? '',
                    (int)($cfg['nida']['timeout'] ?? 10)
                ),
                default => new MockProvider(),
            };
        });
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/config/identity.php' => config_path('identity.php'),
        ], 'config');
    }
}
