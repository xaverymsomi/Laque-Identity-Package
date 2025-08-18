<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Laque\Identity\Core\MrzParser;

final class MrzParserTest extends TestCase
{
    public function testParsesKnownTd3MrzAndValidatesCheckDigits(): void
    {
        $mrz = "P<UTOERIKSSON<<ANNA<MARIA<<<<<<<<<<<<<<<<<<<\nL898902C36UTO7408122F1204159ZE184226B<<<<<<1";
        $res = MrzParser::parse($mrz);
        $this->assertSame('ERIKSSON', $res->lastName);
        $this->assertStringContainsString('ANNA', (string)$res->firstName);
        $this->assertSame('L898902C3', $res->documentNumber);
        $this->assertSame('1974-08-12', $res->dateOfBirth);
        $this->assertSame('2012-04-15', $res->expiryDate);
    }
}
