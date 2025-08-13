<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Laque\Identity\Core\Tin;

final class TinTest extends TestCase
{
    public function testIsValid(): void
    {
        $this->assertTrue(Tin::isValid('123-456-789'));
        $this->assertFalse(Tin::isValid('123-456-7890'));
        $this->assertFalse(Tin::isValid('ABC-456-789'));
    }
}
