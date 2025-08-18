# NIDA Provider

> Replace the base URL and credentials with your production values.

Config (Laravel style):
```php
return [
  'nida' => [
    'base_url' => env('NIDA_BASE_URL', 'https://nida.example.tz/api/v1'),
    'api_key'  => env('NIDA_API_KEY'),
    'timeout'  => 10,
  ]
];
```

Endpoints (illustrative placeholders; adjust to the actual NIDA docs you receive under contract):
- `POST /verify` — body: `{ "nidaNumber": "...", "dateOfBirth": "YYYY-MM-DD" }`
- `POST /search` — optional second-factor queries

Security:
- API key over HTTPS
- HMAC request signing (optional extension)
- Respect data protection laws (minimize payload; mask logs)
