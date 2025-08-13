<?php
declare(strict_types=1);

namespace Laque\Identity\Dto;

final class IdentityRecord
{
    /** @param array<string, mixed> $extra */
    public function __construct(
        public readonly string $fullName,
        public readonly \DateTimeInterface $dateOfBirth,
        public readonly ?string $phone = null,
        public readonly ?string $nin = null,
        public readonly ?string $tin = null,
        public readonly array $extra = []
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'fullName' => $this->fullName,
            'dateOfBirth' => $this->dateOfBirth->format('Y-m-d'),
            'phone' => $this->phone,
            'nin' => $this->nin,
            'tin' => $this->tin,
            'extra' => $this->extra,
        ];
    }
}
