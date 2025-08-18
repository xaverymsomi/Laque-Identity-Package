<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Laque\Identity\Core\Phone;
use Laque\Identity\Core\Tin;

final class PhoneTinTest extends TestCase
{
    public function testNormalizesTanzanianPhoneNumbers(): void
    {
        $this->assertSame('+255712345678', Phone::normalize('0712 345 678'));
        $this->assertSame('+255712345678', Phone::normalize('255712345678'));
        $this->assertSame('+255712345678', Phone::normalize('+255712345678'));
    }

    public function testValidatesTinFormat(): void
    {
        $this->assertTrue(Tin::isValid('123-456-789'));
        $this->assertTrue(Tin::isValid('123456789'));
        $this->assertFalse(Tin::isValid('12345678'));
    }
}
