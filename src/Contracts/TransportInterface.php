<?php
declare(strict_types=1);

namespace Laque\Identity\Contracts;

interface TransportInterface
{
    /** @param array<string,mixed> $headers */
    public function post(string $uri, array $payload, array $headers = []): array;
}
