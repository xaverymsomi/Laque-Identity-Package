<?php
declare(strict_types=1);

namespace Laque\Identity\Dto;

final class MatchScore
{
    public function __construct(
        private float $score,
        private bool $matched,
        private array $reasons = []
    ) {}

    public static function notFound(): self
    {
        return new self(0.0, false, ['not_found']);
    }

    public function score(): float { return $this->score; }
    public function matched(): bool { return $this->matched; }
    public function reasons(): array { return $this->reasons; }
}
