<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Laque\Identity\Security\HmacSigner;
use Laque\Identity\Support\ArrayCache;
use Laque\Identity\Providers\NidaProvider;
use Laque\Identity\Contracts\TransportInterface;
use Laque\Identity\Dto\IdentityQuery;

final class HmacAndCacheTest extends TestCase
{
    public function testComputesStableHmacSignatures(): void
    {
        $expected = hash_hmac('sha256', '{"a":1}', 'secret');
        $this->assertSame($expected, HmacSigner::sign('{"a":1}', 'secret'));
    }

    public function testCachesNidaResponsesByNidaNumberAndDob(): void
    {
        $fake = new class implements TransportInterface {
            public int $calls = 0;
            public function post(string $uri, array $payload, array $headers = []): array {
                $this->calls++;
                return [
                    'firstName' => 'ANNA',
                    'lastName' => 'ERIKSSON',
                    'dateOfBirth' => '1974-08-12',
                    'gender' => 'F'
                ];
            }
        };

        $cache = new ArrayCache();
        $provider = new NidaProvider($fake, $cache, 3600);

        $q = new IdentityQuery(nidaNumber: '12345678901234567890', dateOfBirth: '1974-08-12');
        $provider->find($q);
        $provider->find($q);

        $this->assertSame(1, $fake->calls);
    }
}
