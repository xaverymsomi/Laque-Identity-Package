# vicent/Laque-identity — NIDA & KYC Helpers

<p align="center"><img src="assets/logo.svg" width="280" alt="Laque Identity logo" /></p>

[![Latest Stable Version](https://img.shields.io/packagist/v/vicent/laque-identity.svg)](https://packagist.org/packages/vicent/laque-identity)
[![Release Drafter](https://img.shields.io/badge/Release-Drafter-6A5ACD)](#)
[![codecov](https://codecov.io/gh/vicent-dev/laque-identity/branch/main/graph/badge.svg)](https://codecov.io/gh/vicent-dev/laque-identity)


[![PHP](https://img.shields.io/badge/PHP-8.1%2B-blue)](#)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](LICENSE)
[![CI](https://img.shields.io/github/actions/workflow/status/vicent-dev/laque-identity/tests.yml?branch=main)](#)

A lightweight, **SOLID** PHP library for Tanzania identity/KYC tasks:
- Standardizes **NIDA** verification via pluggable providers (real, CSV, mock)
- **TZ phone** normalization (+255…) and **operator** detection
- **TIN** validation
- **KYC scoring** (name similarity, DoB, phone match)
- **Caching** (PSR‑16) and **rate limiting**

---

## Installation

### Via Packagist (recommended published)
```bash
composer require vicent/laque-identity
```

### Via VCS/Path (before publishing)
```json
{
  "repositories": [{ "type": "path", "url": "../vicent-laque-identity" }],
  "require": { "vicent/laque-identity": "*" }
}
```
```bash
composer require vicent/laque-identity:* --prefer-source
```

**Requirements:** PHP 8.1+, `ext-mbstring`, `ext-json`

---

## Quick start
```php
use Laque\Identity\Core\IdentityService;
use Laque\Identity\Providers\MockProvider;
use Laque\Identity\Dto\IdentityQuery;

$svc = new IdentityService(new MockProvider());
$score = $svc->verify(new IdentityQuery(
  nin: '19990101999999999999',
  fullName: 'Jane Doe',
  dateOfBirth: new DateTimeImmutable('1999-01-01'),
  phone: '0712 345 678',
  tin: '123-456-789'
));

print_r($score->toArray());
```

> Run `php bin/smoke.php` for an end‑to‑end sanity check.

---

## Features

### Providers (Strategy)
- `NidaProvider` — call your NIDA endpoint via a `TransportInterface`
- `CsvProvider` — local CSV fallback (`data/people.csv` sample included)
- `MockProvider` — deterministic data for tests

Swap providers without changing business code (DIP).

### DTOs (Immutable)
- `IdentityQuery`, `IdentityRecord`, `MatchScore`

### Utilities
- `Phone::normalize('0712 345 678')  // +255712345678`
- `Phone::operator('0751234567')     // vodacom`  
  Prefixes come from an internal map and optional data file: **`data/operator_prefixes.php`**.  
  3‑digit blocks are matched first, then 2‑digit families.
- `Tin::isValid('123-456-789')`

### KYC Scoring
Jaro‑Winkler name similarity + exact DoB + phone match. Defaults:
- name 60%, DoB 30%, phone 10% → `compositeScore` 0..100

Override by composing your own `Scoring` and calling it in your service.

### Caching & Rate limiting
Provide any PSR‑16 cache through the adapter; rate limiting is **applied on every call** (even cache hits) to enforce quotas.

---

## Laravel Integration

1. Register the service provider (auto‑discovery if supported):
```php
Laque\Identity\Adapters\Laravel\ServiceProvider::class
```
2. Publish config:
```bash
php artisan vendor:publish --provider="Laque\Identity\Adapters\Laravel\ServiceProvider" --tag=config
```
3. Configure `.env`:
```
IDENTITY_DRIVER=mock   # mock|csv|nida
IDENTITY_CSV_PATH=/full/path/to/people.csv
NIDA_ENDPOINT=https://...
NIDA_API_KEY=secret
```
4. Resolve and use:
```php
use Laque\Identity\Core\IdentityService;
use Laque\Identity\Dto\IdentityQuery;

$svc = app(IdentityService::class); // or manually new with your provider
$score = $svc->verify(new IdentityQuery(nin: '19990101999999999999'));
```

### Using a PSR‑18 HTTP client (NidaProvider)
```bash
composer require guzzlehttp/guzzle nyholm/psr7 http-interop/http-factory-guzzle
```
```php
use Laque\Identity\Adapters\Psr18Transport;
use Laque\Identity\Providers\NidaProvider;
use Laque\Identity\Core\IdentityService;

$transport = new Psr18Transport(
  new \GuzzleHttp\Client(),
  new \Http\Factory\Guzzle\RequestFactory(),
  new \Http\Factory\Guzzle\StreamFactory()
);

$provider = new NidaProvider($transport, getenv('NIDA_ENDPOINT'), getenv('NIDA_API_KEY'));
$svc = new IdentityService($provider);
```

---

## Operator prefixes data file

Edit `data/operator_prefixes.php` to add/remove mappings **without touching code**:
```php
return [
  '3digit' => ['074' => 'vodacom', '075' => 'vodacom', '065' => 'tigo', '071' => 'tigo', '068' => 'airtel', '078' => 'airtel', '062' => 'halotel', '073' => 'ttcl', '077' => 'zantel'],
  '2digit' => ['74' => 'vodacom', '75' => 'vodacom', '65' => 'tigo', '71' => 'tigo', '68' => 'airtel', '78' => 'airtel', '62' => 'halotel', '73' => 'ttcl', '77' => 'zantel'],
];
```

---

## Testing
```bash
composer install
vendor/bin/phpunit
```

CI workflow is included at `.github/workflows/tests.yml` (PHP 8.1/8.2/8.3).

---

## Versioning
This package follows **SemVer**. Tag releases as `vMAJOR.MINOR.PATCH` (e.g., `v0.1.0`).

---

## Contributing
PRs welcome! Please:
- Write tests for new features/bugfixes
- Follow PSR‑12, keep classes small (SRP), prefer constructor DI
- Avoid breaking public APIs unless for a major release

---

## Security
If you discover a security issue, please **do not** open a public issue. Email the maintainer privately (add your email/contact).

---

## License
MIT © 2025 vicent/Laque-identity

---

## Publishing to Packagist (maintainers)

1. Push this repo to a **public Git host** (GitHub/GitLab). Default branch: `main`.
2. Create a **release tag**, e.g.:
   ```bash
   git tag v0.1.0
   git push origin v0.1.0
   ```
3. Submit the repository URL to **Packagist**: https://packagist.org/packages/submit
4. Ensure **auto‑update** is enabled (Packagist reads GitHub webhooks automatically).
5. Users can then install via:
   ```bash
   composer require vicent/laque-identity
   ```
