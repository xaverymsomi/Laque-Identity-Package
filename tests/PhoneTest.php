<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Laque\Identity\Core\Phone;

final class PhoneTest extends TestCase
{
    public function testNormalize(): void
    {
        $this->assertSame('+255712345678', Phone::normalize('0712 345 678'));
        $this->assertSame('+255751234567', Phone::normalize('+255 75 123 4567'));
        $this->assertSame('+255771234567', Phone::normalize('0771234567')); // assume local leading 0
    }

    public function testOperator(): void
    {
        $this->assertSame('tigo', Phone::operator('0712345678'));
        $this->assertSame('vodacom', Phone::operator('0751234567'));
    }
}
