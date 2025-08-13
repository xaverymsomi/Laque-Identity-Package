<?php
declare(strict_types=1);

namespace Laque\Identity\Dto;

final class MatchScore
{
    private function __construct(
        public readonly bool $found,
        public readonly float $nameScore,
        public readonly bool $dobMatch,
        public readonly bool $phoneMatch,
        public readonly int $compositeScore
    ) {}

    public static function notFound(): self
    {
        return new self(false, 0.0, false, false, 0);
    }

    public static function fromComponents(
        float $nameScore,
        bool $dobMatch,
        bool $phoneMatch
    ): self {
        $nameW = 0.6;
        $dobW  = 0.3;
        $phW   = 0.1;

        $composite = (int) round(
            ($nameScore * $nameW * 100) +
            (($dobMatch ? 1 : 0) * $dobW * 100) +
            (($phoneMatch ? 1 : 0) * $phW * 100)
        );

        return new self(true, $nameScore, $dobMatch, $phoneMatch, $composite);
    }


public static function fromArray(array $arr): self
{
    return new self(
        (bool)($arr['found'] ?? false),
        (float)($arr['nameScore'] ?? 0.0),
        (bool)($arr['dobMatch'] ?? false),
        (bool)($arr['phoneMatch'] ?? false),
        (int)($arr['compositeScore'] ?? 0)
    );
}

/** @return array<string, mixed> */
public function toArray(): array

    {
        return [
            'found' => $this->found,
            'nameScore' => $this->nameScore,
            'dobMatch' => $this->dobMatch,
            'phoneMatch' => $this->phoneMatch,
            'compositeScore' => $this->compositeScore,
        ];
    }
}
