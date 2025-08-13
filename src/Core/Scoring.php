<?php
declare(strict_types=1);

namespace Laque\Identity\Core;

use Laque\Identity\Dto\IdentityQuery;
use Laque\Identity\Dto\IdentityRecord;
use Laque\Identity\Dto\MatchScore;

final class Scoring
{
    public static function compare(IdentityQuery $q, IdentityRecord $r): MatchScore
    {
        $nameQ = self::normalizeName($q->fullName ?? '');
        $nameR = self::normalizeName($r->fullName);
        $nameScore = $nameQ && $nameR ? self::jaroWinkler($nameQ, $nameR) : 0.0;

        $dobMatch = false;
        if ($q->dateOfBirth) {
            $dobMatch = $q->dateOfBirth->format('Y-m-d') === $r->dateOfBirth->format('Y-m-d');
        }

        $phoneMatch = false;
        if ($q->phone && $r->phone) {
            $phoneMatch = Phone::normalize($q->phone) === Phone::normalize($r->phone);
        }

        return MatchScore::fromComponents($nameScore, $dobMatch, $phoneMatch);
    }

    public static function normalizeName(string $name): string
    {
        $name = mb_strtolower($name);
        $name = preg_replace('/\s+/', ' ', trim($name)) ?? $name;
        $name = preg_replace('/[^a-z \'-]/u', '', $name) ?? $name;
        return $name;
    }

    // Jaro-Winkler similarity 0..1
    public static function jaroWinkler(string $s1, string $s2): float
    {
        $jaro = self::jaro($s1, $s2);
        $prefix = 0;
        for ($i = 0; $i < min(4, strlen($s1), strlen($s2)); $i++) {
            if ($s1[$i] === $s2[$i]) $prefix++; else break;
        }
        $p = 0.1;
        return $jaro + $prefix * $p * (1 - $jaro);
    }

    private static function jaro(string $s1, string $s2): float
    {
        $len1 = strlen($s1);
        $len2 = strlen($s2);
        if ($len1 === 0 && $len2 === 0) return 1.0;
        $matchDistance = (int) floor(max($len1, $len2) / 2) - 1;

        $s1Matches = array_fill(0, $len1, false);
        $s2Matches = array_fill(0, $len2, false);

        $matches = 0;
        for ($i = 0; $i < $len1; $i++) {
            $start = max(0, $i - $matchDistance);
            $end = min($i + $matchDistance + 1, $len2);
            for ($j = $start; $j < $end; $j++) {
                if ($s2Matches[$j]) continue;
                if ($s1[$i] !== $s2[$j]) continue;
                $s1Matches[$i] = true;
                $s2Matches[$j] = true;
                $matches++;
                break;
            }
        }
        if ($matches === 0) return 0.0;

        $t = 0;
        $k = 0;
        for ($i = 0; $i < $len1; $i++) {
            if (!$s1Matches[$i]) continue;
            while (!$s2Matches[$k]) $k++;
            if ($s1[$i] !== $s2[$k]) $t++;
            $k++;
        }
        $transpositions = $t / 2.0;

        return ($matches / $len1 + $matches / $len2 + ($matches - $transpositions) / $matches) / 3.0;
    }
}
