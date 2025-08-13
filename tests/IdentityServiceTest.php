<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Laque\Identity\Core\IdentityService;
use Laque\Identity\Core\InMemoryCache;
use Laque\Identity\Core\RateLimiter;
use Laque\Identity\Providers\MockProvider;
use Laque\Identity\Dto\IdentityQuery;

final class IdentityServiceTest extends TestCase
{
    public function testCaching(): void
    {
        $cache = new InMemoryCache();
        $svc = new IdentityService(new MockProvider(), $cache, null, 3600);
        $q = new IdentityQuery(nin: '19990101999999999999', fullName: 'Jane Doe', dateOfBirth: new DateTimeImmutable('1999-01-01'));
        $first = $svc->verify($q);
        $second = $svc->verify($q);
        $this->assertSame($first->compositeScore, $second->compositeScore);
    }

    public function testRateLimit(): void
    {
        $cache = new InMemoryCache();
        $rl = new RateLimiter($cache, 1, 60);
        $svc = new IdentityService(new MockProvider(), $cache, $rl, 60);
        $q = new IdentityQuery(nin: '19990101999999999999');
        $svc->verify($q);
        $this->expectException(\Laque\Identity\Exceptions\RateLimitExceeded::class);
        $svc->verify($q);
    }
}
