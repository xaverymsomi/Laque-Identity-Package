<?php
declare(strict_types=1);

namespace Laque\Identity\Core;

final class Tin
{
    public static function normalize(string $tin): string
    {
        return preg_replace('/\D+/', '', $tin) ?? $tin;
    }

    public static function isValid(string $tin, bool $useChecksum = false): bool
    {
        $n = self::normalize($tin);
        if (!preg_match('/^\d{9}$/', $n)) {
            return false;
        }
        if ($useChecksum) {
            return self::luhn($n);
        }
        return true;
    }

    private static function luhn(string $number): bool
    {
        $sum = 0; $alt = false;
        for ($i = strlen($number) - 1; $i >= 0; $i--) {
            $n = intval($number[$i]);
            if ($alt) {
                $n *= 2;
                if ($n > 9) $n -= 9;
            }
            $sum += $n;
            $alt = !$alt;
        }
        return $sum % 10 === 0;
    }
}
