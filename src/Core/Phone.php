<?php
declare(strict_types=1);

namespace Laque\Identity\Core;

final class Phone
{
    public static function normalize(?string $raw): ?string
    {
        if ($raw === null) return null;
        $digits = preg_replace('/\D+/', '', $raw) ?? '';
        if ($digits === '') return null;

        // Already includes country code (255...)
        if (str_starts_with($digits, '255') && strlen($digits) === 12) {
            return '+' . $digits;
        }
        // Local format starting with 0 and 10 digits (e.g., 0712345678)
        if (str_starts_with($digits, '0') && strlen($digits) === 10) {
            return '+255' . substr($digits, 1);
        }
        // 9-digit local number (already trimmed leading zero)
        if (strlen($digits) == 9) {
            return '+255' . $digits;
        }
        // If the original already had a plus (best effort)
        if (str_starts_with($raw, '+')) {
            return $raw;
        }
        // Fallback: just prefix +
        return '+' . $digits;
    }
}
