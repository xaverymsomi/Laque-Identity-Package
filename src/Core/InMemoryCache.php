<?php
declare(strict_types=1);

namespace Laque\Identity\Core;

use Laque\Identity\Contracts\CacheInterface;

final class InMemoryCache implements CacheInterface
{
    /** @var array<string, array{value:mixed, expires:int|null}> */
    private array $store = [];

    public function get(string $key, mixed $default = null): mixed
    {
        if (!$this->has($key)) return $default;
        return $this->store[$key]['value'];
    }

    public function set(string $key, mixed $value, null|int|\DateInterval $ttl = null): bool
    {
        $expires = null;
        if ($ttl instanceof \DateInterval) {
            $expires = (new \DateTimeImmutable())->add($ttl)->getTimestamp();
        } elseif (is_int($ttl)) {
            $expires = time() + $ttl;
        }
        $this->store[$key] = ['value' => $value, 'expires' => $expires];
        return true;
    }

    public function has(string $key): bool
    {
        if (!isset($this->store[$key])) return false;
        $expires = $this->store[$key]['expires'];
        if ($expires !== null && $expires < time()) {
            unset($this->store[$key]);
            return false;
        }
        return true;
    }

    public function delete(string $key): bool
    {
        unset($this->store[$key]);
        return true;
    }
}
