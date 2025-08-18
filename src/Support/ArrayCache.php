<?php
declare(strict_types=1);

namespace Laque\Identity\Support;

use DateInterval;
use Psr\SimpleCache\CacheInterface;

final class ArrayCache implements CacheInterface
{
    private array $items = [];

    public function get($key, $default = null): mixed
    {
        if (!$this->has($key)) return $default;
        return $this->items[$key]['value'];
    }

    public function set($key, $value, $ttl = null): bool
    {
        $expires = null;
        if ($ttl instanceof DateInterval) {
            $expires = (new \DateTimeImmutable())->add($ttl)->getTimestamp();
        } elseif (is_int($ttl)) {
            $expires = time() + $ttl;
        }
        $this->items[$key] = ['value' => $value, 'expires' => $expires];
        return true;
    }

    public function delete($key): bool
    {
        unset($this->items[$key]);
        return true;
    }

    public function clear(): bool
    {
        $this->items = [];
        return true;
    }

    public function getMultiple($keys, $default = null): iterable
    {
        $out = [];
        foreach ($keys as $k) $out[$k] = $this->get($k, $default);
        return $out;
    }

    public function setMultiple($values, $ttl = null): bool
    {
        foreach ($values as $k => $v) $this->set($k, $v, $ttl);
        return true;
    }

    public function deleteMultiple($keys): bool
    {
        foreach ($keys as $k) $this->delete($k);
        return true;
    }

    public function has($key): bool
    {
        if (!array_key_exists($key, $this->items)) return false;
        $exp = $this->items[$key]['expires'];
        if ($exp !== null && time() >= $exp) {
            unset($this->items[$key]);
            return false;
        }
        return true;
    }
}
