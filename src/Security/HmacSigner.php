<?php
declare(strict_types=1);

namespace Laque\Identity\Security;

final class HmacSigner
{
    public static function sign(string $payload, string $secret): string
    {
        return hash_hmac('sha256', $payload, $secret);
    }
}
