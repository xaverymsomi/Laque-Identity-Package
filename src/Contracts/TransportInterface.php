<?php
declare(strict_types=1);

namespace Laque\Identity\Contracts;

/**
 * Minimal transport abstraction to avoid hard dependency on PSR-18 for core.
 */
interface TransportInterface
{
    /**
     * Sends JSON payload to a URL and returns decoded JSON as array.
     *
     * @param array<string, mixed> $payload
     * @param array<string, string> $headers
     * @return array<string, mixed>
     */
    public function postJson(string $url, array $payload, array $headers = [], int $timeoutSeconds = 10): array;
}
