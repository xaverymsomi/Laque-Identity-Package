<?php
declare(strict_types=1);

namespace Laque\Identity\Core;

use Laque\Identity\Dto\MrzResult;

final class MrzParser
{
    public static function parse(string $raw): MrzResult
    {
        $lines = preg_split('/\r?\n/', trim($raw));
        if (!$lines) throw new \InvalidArgumentException('Empty MRZ');
        $count = count($lines);
        if ($count === 2) {
            return self::parseTd3($lines[0], $lines[1]);
        } elseif ($count === 3) {
            return self::parseTd1($lines[0], $lines[1], $lines[2]);
        }
        throw new \InvalidArgumentException('Unsupported MRZ format');
    }

    private static function parseTd3(string $l1, string $l2): MrzResult
    {
        $l1 = self::pad($l1, 44);
        $l2 = self::pad($l2, 44);

        $docType = substr($l1, 0, 1);
        $issuing = substr($l1, 2, 3);
        $names = substr($l1, 5);
        [$last, $first] = self::splitNames($names);

        $docNumRaw = substr($l2, 0, 9);
        $docNum = trim(str_replace('<', '', $docNumRaw));
        $docNumCheck = substr($l2, 9, 1);
        self::assertCheck($docNumRaw, $docNumCheck, 'document number');

        $nationality = substr($l2, 10, 3);
        $dobRaw = substr($l2, 13, 6);
        $dobCheck = substr($l2, 19, 1);
        self::assertCheck($dobRaw, $dobCheck, 'date of birth');
        $sex = substr($l2, 20, 1);
        $expRaw = substr($l2, 21, 6);
        $expCheck = substr($l2, 27, 1);
        self::assertCheck($expRaw, $expCheck, 'expiry date');

        return new MrzResult(
            documentType: $docType,
            issuingCountry: self::norm(self::onlyLetters($issuing)),
            lastName: self::norm($last),
            firstName: self::norm($first),
            documentNumber: $docNum ?: null,
            nationality: self::norm(self::onlyLetters($nationality)),
            dateOfBirth: self::normalizeDate($dobRaw),
            sex: $sex === '<' ? null : $sex,
            expiryDate: self::normalizeDate($expRaw)
        );
    }

    private static function parseTd1(string $l1, string $l2, string $l3): MrzResult
    {
        $l1 = self::pad($l1, 30);
        $l2 = self::pad($l2, 30);
        $l3 = self::pad($l3, 30);

        $docType = substr($l1, 0, 1);
        $issuing = substr($l1, 2, 3);
        $docNumRaw = substr($l1, 5, 9);
        $docNum = trim(str_replace('<', '', $docNumRaw));
        $docNumCheck = substr($l1, 14, 1);
        self::assertCheck($docNumRaw, $docNumCheck, 'document number');

        $dobRaw = substr($l2, 0, 6);
        $dobCheck = substr($l2, 6, 1);
        self::assertCheck($dobRaw, $dobCheck, 'date of birth');
        $sex = substr($l2, 7, 1);
        $expRaw = substr($l2, 8, 6);
        $expCheck = substr($l2, 14, 1);
        self::assertCheck($expRaw, $expCheck, 'expiry date');
        $nationality = substr($l2, 15, 3);

        [$last, $first] = self::splitNames($l3);

        return new MrzResult(
            documentType: $docType,
            issuingCountry: self::norm(self::onlyLetters($issuing)),
            lastName: self::norm($last),
            firstName: self::norm($first),
            documentNumber: $docNum ?: null,
            nationality: self::norm(self::onlyLetters($nationality)),
            dateOfBirth: self::normalizeDate($dobRaw),
            sex: $sex === '<' ? null : $sex,
            expiryDate: self::normalizeDate($expRaw)
        );
    }

    private static function splitNames(string $raw): array
    {
        $parts = explode("<<", $raw, 2);
        $last = str_replace('<', ' ', $parts[0] ?? '');
        $first = str_replace('<', ' ', $parts[1] ?? '');
        return [trim($last), trim($first)];
    }

    private static function pad(string $s, int $len): string
    {
        $s = trim($s);
        if (strlen($s) < $len) $s .= str_repeat('<', $len - strlen($s));
        return substr($s, 0, $len);
    }

    private static function normalizeDate(?string $yyMMdd): ?string
    {
        if (!$yyMMdd || strlen($yyMMdd) !== 6) return null;
        $yy = (int)substr($yyMMdd, 0, 2);
        $mm = substr($yyMMdd, 2, 2);
        $dd = substr($yyMMdd, 4, 2);
        $century = $yy < 30 ? 2000 : 1900; // heuristic
        return sprintf('%04d-%s-%s', $century + $yy, $mm, $dd);
    }

    private static function onlyLetters(string $s): string
    {
        return preg_replace('/[^A-Z]/', '', $s) ?? '';
    }

    private static function norm(?string $s): ?string
    {
        if ($s === null) return null;
        $s = trim($s);
        return $s === '' ? null : $s;
    }

    private static function assertCheck(string $fieldRaw, string $checkDigit, string $label): void
    {
        $calc = self::calcCheckDigit($fieldRaw);
        if ($checkDigit !== (string)$calc) {
            throw new \InvalidArgumentException("MRZ {$label} check digit mismatch");
        }
    }

    private static function calcCheckDigit(string $fieldRaw): int
    {
        $weights = [7, 3, 1];
        $sum = 0;
        $len = strlen($fieldRaw);
        for ($i = 0; $i < $len; $i++) {
            $c = $fieldRaw[$i];
            $val = self::charValue($c);
            $sum += $val * $weights[$i % 3];
        }
        return $sum % 10;
    }

    private static function charValue(string $c): int
    {
        if ($c === '<') return 0;
        if ($c >= '0' && $c <= '9') return ord($c) - ord('0');
        if ($c >= 'A' && $c <= 'Z') return ord($c) - ord('A') + 10;
        return 0;
    }
}
