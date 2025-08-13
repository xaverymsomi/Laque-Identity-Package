<?php
declare(strict_types=1);

namespace Laque\Identity\Dto;

final class IdentityQuery
{
    public function __construct(
        public readonly ?string $nin = null,
        public readonly ?string $fullName = null,
        public readonly ?\DateTimeInterface $dateOfBirth = null,
        public readonly ?string $phone = null,
        public readonly ?string $tin = null
    ) {}

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data): self
    {
        return new self(
            nin: $data['nin'] ?? null,
            fullName: $data['fullName'] ?? null,
            dateOfBirth: isset($data['dateOfBirth']) && $data['dateOfBirth']
                ? new \DateTimeImmutable((string)$data['dateOfBirth'])
                : null,
            phone: $data['phone'] ?? null,
            tin: $data['tin'] ?? null,
        );
    }

    public function key(): string
    {
        $dob = $this->dateOfBirth?->format('Y-m-d') ?? '';
        return hash('sha256', ($this->nin ?? '').'|'.($this->fullName ?? '').'|'.$dob.'|'.($this->phone ?? '').'|'.($this->tin ?? ''));
    }
}
