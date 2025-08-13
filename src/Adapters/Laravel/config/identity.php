<?php
return [
    'driver' => env('IDENTITY_DRIVER', 'mock'), // mock|csv|nida
    'csv_path' => env('IDENTITY_CSV_PATH', storage_path('identity.csv')),
    'nida' => [
        'endpoint' => env('NIDA_ENDPOINT', ''),
        'api_key'  => env('NIDA_API_KEY', ''),
        'timeout'  => (int) env('NIDA_TIMEOUT', 10),
    ],
    'rate_limit' => ['max' => 60, 'per_seconds' => 60],
    'cache_ttl'  => 86400,
];
