# Laravel Integration

1) Publish config:
```php
// config/laque_identity.php
return [
  'nida' => [
    'base_url' => env('NIDA_BASE_URL', 'https://nida.example.tz/api/v1'),
    'api_key'  => env('NIDA_API_KEY'),
    'timeout'  => 10,
  ]
];
```

2) Bind service:
```php
$this->app->singleton(Laque\Identity\Core\IdentityService::class, function($app) {
    $cfg = config('laque_identity.nida');
    $transport = new Laque\Identity\Adapters\Psr18\HttpTransport($cfg['base_url'], $cfg['api_key']);
    return new Laque\Identity\Core\IdentityService(new Laque\Identity\Providers\NidaProvider($transport));
});
```


## Publish config

```bash
php artisan vendor:publish --provider="Laque\Identity\Adapters\Laravel\LaqueIdentityServiceProvider" --tag=config
```

## Auto-discovery

The service provider is auto-discovered via composer `extra.laravel.providers`.

## Environment

```
NIDA_BASE_URL=https://nida.example.tz/api/v1
NIDA_API_KEY=replace-me
NIDA_TIMEOUT=10
NIDA_CACHE_TTL=900
NIDA_HMAC_SECRET=optional-secret
```
