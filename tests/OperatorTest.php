<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Laque\Identity\Core\Operator;

final class OperatorTest extends TestCase
{
    public function testDetectsOperatorsFromDefaultPrefixMap(): void
    {
        $this->assertSame('Tigo', Operator::detect('0712 345 678'));
        $this->assertSame('Vodacom', Operator::detect('+255752000000'));
        $this->assertSame('Airtel', Operator::detect('0782000000'));
        $this->assertSame('Halotel', Operator::detect('0622000000'));
        $this->assertSame('TTCL', Operator::detect('0732000000'));
        $this->assertSame('Zantel', Operator::detect('0772000000'));
    }
}
