<?php
declare(strict_types=1);

namespace Laque\Identity\Adapters\Laravel;

use Illuminate\Support\ServiceProvider;
use Laque\Identity\Core\IdentityService;
use Laque\Identity\Providers\NidaProvider;
use Laque\Identity\Adapters\Psr18\HttpTransport;
use Psr\Http\Client\ClientInterface as HttpClient;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Log\LoggerInterface;
use Psr\SimpleCache\CacheInterface;

final class LaqueIdentityServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../../config/laque_identity.php', 'laque_identity');

        $this->app->singleton(IdentityService::class, function ($app) {
            $cfg = $app['config']->get('laque_identity.nida', []);

            /** @var HttpClient $http */
            $http = $app->make(HttpClient::class);
            /** @var RequestFactoryInterface $reqF */
            $reqF = $app->make(RequestFactoryInterface::class);
            /** @var StreamFactoryInterface $streamF */
            $streamF = $app->make(StreamFactoryInterface::class);
            /** @var LoggerInterface|null $logger */
            $logger = $app->bound(LoggerInterface::class) ? $app->make(LoggerInterface::class) : null;
            /** @var CacheInterface|null $cache */
            $cache = $app->bound(CacheInterface::class) ? $app->make(CacheInterface::class) : null;

            $transport = new HttpTransport(
                baseUrl: (string)($cfg['base_url'] ?? 'https://nida.example.tz/api/v1'),
                apiKey: (string)($cfg['api_key'] ?? ''),
                client: $http,
                requestFactory: $reqF,
                streamFactory: $streamF,
                timeout: (int)($cfg['timeout'] ?? 10),
                signerSecret: (string)($cfg['hmac_secret'] ?? '')
            );

            $provider = new NidaProvider($transport, $cache, (int)($cfg['ttl'] ?? 900));

            return new IdentityService($provider, $logger);
        });
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../../../config/laque_identity.php' => config_path('laque_identity.php'),
        ], 'config');
    }
}
