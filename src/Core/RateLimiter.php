<?php
declare(strict_types=1);

namespace Laque\Identity\Core;

use Laque\Identity\Contracts\CacheInterface;
use Laque\Identity\Exceptions\RateLimitExceeded;

final class RateLimiter
{
    public function __construct(
        private CacheInterface $cache,
        private int $max,
        private int $perSeconds
    ) {}

    public function hitOrThrow(string $key): void
    {
        $now = time();
        $bucketKey = "rl:$key:" . intdiv($now, $this->perSeconds);
        $count = (int) ($this->cache->get($bucketKey, 0) ?? 0);
        if ($count >= $this->max) {
            throw new RateLimitExceeded("Rate limit exceeded for key {$key}");
        }
        $this->cache->set($bucketKey, $count + 1, $this->perSeconds + 1);
    }
}
