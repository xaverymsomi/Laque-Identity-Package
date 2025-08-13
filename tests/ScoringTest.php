<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Laque\Identity\Core\Scoring;
use Laque\Identity\Dto\IdentityQuery;
use Laque\Identity\Dto\IdentityRecord;

final class ScoringTest extends TestCase
{
    public function testScoring(): void
    {
        $q = new IdentityQuery(fullName: 'Jane Doe', dateOfBirth: new DateTimeImmutable('1999-01-01'), phone: '0712345678');
        $r = new IdentityRecord('Jane Doe', new DateTimeImmutable('1999-01-01'), '0712345678');
        $score = Scoring::compare($q, $r);
        $this->assertSame(true, $score->dobMatch);
        $this->assertSame(true, $score->phoneMatch);
        $this->assertGreaterThan(0.99, $score->nameScore);
        $this->assertGreaterThan(90, $score->compositeScore);
    }
}
