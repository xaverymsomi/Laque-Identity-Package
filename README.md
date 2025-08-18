# Laque Identity

![Logo](branding/laque-identity.svg)

[![Latest Stable Version](https://img.shields.io/packagist/v/vicent/laque-identity.svg)](https://packagist.org/packages/vicent/laque-identity)
[![Release Drafter](https://img.shields.io/badge/Release-Drafter-6A5ACD)](#)
[![codecov](https://codecov.io/gh/vicent-dev/laque-identity/branch/main/graph/badge.svg)](https://codecov.io/gh/vicent-dev/laque-identity)

[![PHP](https://img.shields.io/badge/PHP-8.1%2B-blue)](#)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](LICENSE)
[![CI](https://img.shields.io/github/actions/workflow/status/vicent-dev/laque-identity/tests.yml?branch=main)](#)

**Laque Identity** is a Tanzania‑focused PHP library for **KYC**:
- **MRZ parsing & validation** (TD1 for ID cards, TD3 for passports)
- **NIDA client** (pluggable; replace base URL with your production endpoint)
- **TIN & Phone validation** for Tanzania (with **operator detection**)
- **KYC scoring** (names, DoB, phone, document #)

Framework‑agnostic, SOLID, PSR‑compliant.

---

## Installation

```bash
composer require vicent/laque-identity
```

---

## Quick Start

```php
use Laque\Identity\Core\IdentityService;
use Laque\Identity\Providers\MockProvider;
use Laque\Identity\Dto\IdentityQuery;

// Build a service with a provider (Mock here; swap with NidaProvider)
$service = new IdentityService(new MockProvider());

// Query using MRZ (passport TD3) or NIDA number/DoB
$q = new IdentityQuery(
    nidaNumber: '19876543210987654321',
    dateOfBirth: '1990-01-01',
    firstName: 'FROLIAN',
    lastName: 'ERNEZ',
    phone: '0712 345 678',
    tin: '123-456-789',
    mrz: null // or provide two-line MRZ string
);

$result = $service->verify($q);

// $result->matched(): bool
// $result->score(): float  // 0..1
// $result->reasons(): array
```

---

## MRZ Parsing

```php
use Laque\Identity\Core\MrzParser;

$td3 = "P<TZAERNEZ<<FROLIAN<<<<<<<<<<<<<<<<<<\nC1234567<8TZA9001012M2601012<<<<<<<<<<";
$parsed = MrzParser::parse($td3);
// $parsed->documentNumber, $parsed->dateOfBirth, $parsed->expiryDate, etc.
```

> Supports **TD1** (3×30) and **TD3** (2×44). Check digits validated per ICAO 9303.

---

## NIDA Provider

Configure the **base URL** and credentials (replace with actual production values when ready).

```php
use Laque\Identity\Providers\NidaProvider;
use Laque\Identity\Adapters\Psr18\HttpTransport;
use Laque\Identity\Core\IdentityService;

$transport = new HttpTransport(
    baseUrl: getenv('NIDA_BASE_URL') ?: 'https://nida.example.tz/api/v1', // REPLACE
    apiKey: getenv('NIDA_API_KEY') ?: 'replace-me'
);

$service = new IdentityService(new NidaProvider($transport));
```

- All requests support **PSR-18** HTTP clients and **PSR-3** logging.
- Responses are mapped to a common `IdentityRecord` DTO.

---

## Laravel Integration (optional)

```php
// config/laque_identity.php
return [
  'nida' => [
    'base_url' => env('NIDA_BASE_URL', 'https://nida.example.tz/api/v1'), // REPLACE
    'api_key'  => env('NIDA_API_KEY'),
    'timeout'  => 10,
  ]
];
```

```php
// In your AppServiceProvider or a dedicated ServiceProvider
$this->app->singleton(Laque\Identity\Core\IdentityService::class, function($app) {
    $cfg = config('laque_identity.nida');
    $transport = new Laque\Identity\Adapters\Psr18\HttpTransport($cfg['base_url'], $cfg['api_key']);
    return new Laque\Identity\Core\IdentityService(
        new Laque\Identity\Providers\NidaProvider($transport),
        $app->make(Psr\Log\LoggerInterface::class) // optional
    );
});
```

---

## Logging

Pass any **PSR‑3** logger (e.g., Monolog) to `IdentityService` to record requests, response codes, and match outcomes. Correlate with a `trace_id` for audits.

---

## Testing

```bash
composer install
composer test
```

---

## Documentation

- See [`docs/index.md`](docs/index.md) for full guide
- [`docs/mrz.md`](docs/mrz.md) — MRZ formats and parser
- [`docs/nida.md`](docs/nida.md) — NIDA flows & endpoints (placeholders)
- [`docs/kyc.md`](docs/kyc.md) — KYC scoring
- [`docs/laravel.md`](docs/laravel.md) — Laravel setup

---

## Security

- Do not log PII (full MRZ, NIDA number). Mask sensitive fields.
- Use HTTPS and rotate API keys.
- Rate-limit NIDA requests and cache stable results per policy.

---

**Author**: Vicent Msomi — <msomivicent@gmail.com>  
**License**: MIT


### Publish config
```bash
php artisan vendor:publish --provider="Laque\Identity\Adapters\Laravel\LaqueIdentityServiceProvider" --tag=config
```

### Caching & HMAC
- Set `NIDA_CACHE_TTL` to cache verification responses.
- Set `NIDA_HMAC_SECRET` to enable `X-Signature` HMAC-SHA256 signing.


### Running tests
- Local (fast, no coverage required):
```bash
composer test
```
- With coverage (requires Xdebug or pcov):
```bash
composer test:coverage
```
