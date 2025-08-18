<?php
declare(strict_types=1);

namespace Laque\Identity\Dto;

final class IdentityRecord
{
    /** @param array<string,mixed> $extra */
    public function __construct(
        public readonly string $firstName,
        public readonly string $lastName,
        public readonly string $dateOfBirth, // YYYY-MM-DD
        public readonly ?string $gender = null,
        public readonly ?string $nationality = null,
        public readonly ?string $documentNumber = null,
        public readonly ?string $expiryDate = null,
        public readonly ?string $phone = null,
        public readonly array $extra = []
    ) {}
}
