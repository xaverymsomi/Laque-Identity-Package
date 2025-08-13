# Changelog

All notable changes to this project will be documented in this file.

## v0.1.0 — 2025-08-13
- Initial public release
- Providers: NidaProvider (PSR-18 transport), CsvProvider, MockProvider
- DTOs: IdentityQuery, IdentityRecord, MatchScore
- Core: IdentityService (cache + rate limit), Phone, Tin, Scoring
- Adapters: PSR-16 cache adapter, Laravel ServiceProvider
- Tests: PHPUnit (Phone/Tin/Scoring/Service)
- CI: GitHub Actions for PHP 8.1–8.3
