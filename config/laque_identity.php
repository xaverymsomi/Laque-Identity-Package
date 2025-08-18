<?php
return [
  'nida' => [
    'base_url' => env('NIDA_BASE_URL', 'https://nida.example.tz/api/v1'), // REPLACE
    'api_key' => env('NIDA_API_KEY'),
    'timeout' => env('NIDA_TIMEOUT', 10),
    'ttl' => env('NIDA_CACHE_TTL', 900), // seconds
    'hmac_secret' => env('NIDA_HMAC_SECRET', ''), // optional HMAC signing
  ],
];
