<?php
declare(strict_types=1);

namespace Laque\Identity\Core;

final class Tin
{
    public static function isValid(?string $tin): bool
    {
        if ($tin === null) return false;
        $digits = preg_replace('/\D+/', '', $tin) ?? '';
        return (bool)preg_match('/^\d{9}$/', $digits); // 9 digits (e.g., 123456789 or 123-456-789)
    }
}
