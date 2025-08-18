<?php
declare(strict_types=1);

namespace Laque\Identity\Providers;

use Laque\Identity\Contracts\IdentityProviderInterface;
use Laque\Identity\Contracts\TransportInterface;
use Laque\Identity\Dto\IdentityQuery;
use Laque\Identity\Dto\IdentityRecord;
use Psr\SimpleCache\CacheInterface;

final class NidaProvider implements IdentityProviderInterface
{
    public function __construct(
        private TransportInterface $http,
        private ?CacheInterface $cache = null,
        private int $ttl = 900
    ) {}

    public function find(IdentityQuery $q): ?IdentityRecord
    {
        $payload = array_filter([
            'nidaNumber' => $q->nidaNumber,
            'dateOfBirth' => $q->dateOfBirth,
            'firstName' => $q->firstName,
            'lastName' => $q->lastName,
            'phone' => $q->phone,
            'tin' => $q->tin
        ], fn($v) => $v !== null && $v !== '');

        $cacheKey = null;
        if ($q->nidaNumber && $q->dateOfBirth) {
            $cacheKey = 'nida:' . $q->nidaNumber . ':' . $q->dateOfBirth;
            if ($this->cache && $this->cache->has($cacheKey)) {
                $res = $this->cache->get($cacheKey);
                return $this->map($res);
            }
        }

        // Placeholder endpoint; replace with actual when you integrate
        $res = $this->http->post('/verify', $payload);

        if ($cacheKey && $this->cache) {
            $this->cache->set($cacheKey, $res, $this->ttl);
        }

        return $this->map($res);
    }

    /** @param array<string,mixed>|null $res */
    private function map($res): ?IdentityRecord
    {
        if (!is_array($res) || !isset($res['firstName'], $res['lastName'], $res['dateOfBirth'])) {
            return null;
        }

        return new IdentityRecord(
            firstName: (string)$res['firstName'],
            lastName: (string)$res['lastName'],
            dateOfBirth: (string)$res['dateOfBirth'],
            gender: $res['gender'] ?? null,
            nationality: $res['nationality'] ?? null,
            documentNumber: $res['documentNumber'] ?? null,
            expiryDate: $res['expiryDate'] ?? null,
            phone: $res['phone'] ?? null,
            extra: $res
        );
    }
}
