<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Laque\Identity\Core\IdentityService;
use Laque\Identity\Providers\MockProvider;
use Laque\Identity\Dto\IdentityQuery;

final class IdentityServiceTest extends TestCase
{
    public function testVerifiesIdentityWithMockProviderAndYieldsStrongMatch(): void
    {
        $svc = new IdentityService(new MockProvider());
        $q = new IdentityQuery(firstName: 'ANNA', lastName: 'ERIKSSON', dateOfBirth: '1974-08-12', phone: '0712 345 678');
        $score = $svc->verify($q);
        $this->assertTrue($score->matched());
        $this->assertGreaterThanOrEqual(0.85, $score->score());
    }
}
