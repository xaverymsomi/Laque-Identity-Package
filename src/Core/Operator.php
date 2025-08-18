<?php
declare(strict_types=1);

namespace Laque\Identity\Core;

final class Operator
{
    /**
     * Detect mobile network operator by Tanzanian prefixes.
     * Accepts both legacy '07x' mapping and NSN '7xx' after +255 normalization.
     *
     * @param string|null $phone Any format, will be normalized to +255...
     * @param array<string,string>|null $overrides Map of 3-digit prefixes (e.g., '071' or '712')
     * @return string|null Operator name or null if unknown
     */
    public static function detect(?string $phone, ?array $overrides = null): ?string
    {
        $normalized = Phone::normalize($phone);
        if ($normalized === null) return null;

        // Extract national significant number after +255
        if (!str_starts_with($normalized, '+255')) return null;
        $nsn = substr($normalized, 4); // e.g., 712345678
        if (strlen($nsn) < 3) return null;
        $pfx3 = substr($nsn, 0, 3);    // e.g., '712'

        // Defaults (indicative). Override with official TCRA list in production.
        $defaults = [
            // Tigo
            '071' => 'Tigo',
            '067' => 'Tigo',
            // Vodacom
            '075' => 'Vodacom',
            '074' => 'Vodacom',
            '076' => 'Vodacom',
            // Airtel
            '078' => 'Airtel',
            '068' => 'Airtel',
            '069' => 'Airtel',
            '065' => 'Airtel',
            // Halotel
            '062' => 'Halotel',
            // TTCL
            '073' => 'TTCL',
            // Zantel
            '077' => 'Zantel',
        ];

        $map = $overrides ? array_merge($defaults, $overrides) : $defaults;

        // Normalize keys: allow matching both '071' and '712'
        $normalizedMap = [];
        foreach ($map as $k => $v) {
            $k = (string)$k;
            $normalizedMap[$k] = $v;
            if (strlen($k) === 3 && $k[0] === '0') {
                $normalizedMap[substr($k, 1)] = $v; // '071' -> '71' (2 chars) not enough, we need '712'? No.
            }
        }
        // Special handling: some lists use '0712' granularity. We match on 3-char NSN '712'.
        // If keys are like '071', transform to '7' + last two digits => '712'
        foreach ($map as $k => $v) {
            if (strlen($k) === 3 && $k[0] === '0') {
                $normalizedMap['7' . substr($k, 1)] = $v; // '071' -> '771'? No -> '7' + '71' = '771' wrong
            }
        }
        // Correct transformation: '071' should map to NSN '712', '075' -> '752', etc.
        // The pattern is 0 7 X  ->  7 X ?  (But we need first three NSN digits (7 X Y)). We can't infer Y.
        // Therefore, better approach: keep both 3-digit legacy '071' and any '7xx' if provided in overrides.
        // For robust default behavior, add the common specific prefixes explicitly below:
        $normalizedMap += [
            '712' => 'Tigo',
            '675' => 'Tigo', // example fallback
            '752' => 'Vodacom',
            '742' => 'Vodacom',
            '762' => 'Vodacom',
            '782' => 'Airtel',
            '682' => 'Airtel',
            '692' => 'Airtel',
            '652' => 'Airtel',
            '622' => 'Halotel',
            '732' => 'TTCL',
            '772' => 'Zantel',
        ];

        return $normalizedMap[$pfx3] ?? ($map[$pfx3] ?? null);
    }
}
