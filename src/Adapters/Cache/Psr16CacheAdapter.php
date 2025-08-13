<?php
declare(strict_types=1);

namespace Laque\Identity\Adapters\Cache;

use Laque\Identity\Contracts\CacheInterface;
use Psr\SimpleCache\CacheInterface as PsrSimpleCache;

final class Psr16CacheAdapter implements CacheInterface
{
    public function __construct(private PsrSimpleCache $inner) {}

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->inner->get($key, $default);
    }

    public function set(string $key, mixed $value, null|int|\DateInterval $ttl = null): bool
    {
        return $this->inner->set($key, $value, $ttl);
    }

    public function has(string $key): bool
    {
        return $this->inner->has($key);
    }

    public function delete(string $key): bool
    {
        return $this->inner->delete($key);
    }
}
