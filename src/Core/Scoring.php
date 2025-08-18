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
        $reasons = [];
        $score = 0.0;

        // Name similarity (0..1)
        $nameSim = 0.0;
        if ($q->firstName && $q->lastName) {
            $nameSim = (self::sim($q->firstName, $r->firstName) + self::sim($q->lastName, $r->lastName)) / 2.0;
        } elseif ($q->firstName) {
            $nameSim = self::sim($q->firstName, $r->firstName);
        } elseif ($q->lastName) {
            $nameSim = self::sim($q->lastName, $r->lastName);
        }
        $score += 0.4 * $nameSim;
        $reasons[] = 'name:' . number_format($nameSim, 2);

        // DoB exact match
        $dobMatch = ($q->dateOfBirth && $q->dateOfBirth === $r->dateOfBirth) ? 1.0 : 0.0;
        $score += 0.4 * $dobMatch;
        $reasons[] = 'dob:' . $dobMatch;

        // Phone normalized match
        $qPhone = Phone::normalize($q->phone);
        $rPhone = Phone::normalize($r->phone);
        $phoneMatch = ($qPhone && $rPhone && $qPhone === $rPhone) ? 1.0 : 0.0;
        $score += 0.1 * $phoneMatch;
        $reasons[] = 'phone:' . $phoneMatch;

        // Document number heuristic (if provided via provider extras)
        $docMatch = 0.0;
        if (!empty($r->documentNumber) && $q->mrz) {
            // If MRZ provided, extract doc number and compare
            try {
                $mrz = MrzParser::parse($q->mrz);
                if ($mrz->documentNumber && $r->documentNumber) {
                    $docMatch = ($mrz->documentNumber === $r->documentNumber) ? 1.0 : 0.0;
                }
            } catch (\Throwable $e) {
                $docMatch = 0.0;
            }
        }
        $score += 0.1 * $docMatch;
        $reasons[] = 'doc:' . $docMatch;

        $matched = $score >= 0.85;
        return new MatchScore($score, $matched, $reasons);
    }

    private static function sim(string $a, string $b): float
    {
        $a = strtoupper(trim($a));
        $b = strtoupper(trim($b));
        if ($a === '' || $b === '') return 0.0;
        $dist = levenshtein($a, $b);
        $max = max(strlen($a), strlen($b));
        if ($max === 0) return 0.0;
        $sim = 1.0 - ($dist / $max);
        return max(0.0, min(1.0, $sim));
    }
}
