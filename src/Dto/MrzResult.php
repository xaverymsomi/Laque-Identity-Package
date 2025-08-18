<?php
declare(strict_types=1);

namespace Laque\Identity\Dto;

final class MrzResult
{
    public function __construct(
        public readonly string $documentType,
        public readonly ?string $issuingCountry,
        public readonly ?string $lastName,
        public readonly ?string $firstName,
        public readonly ?string $documentNumber,
        public readonly ?string $nationality,
        public readonly ?string $dateOfBirth, // normalized YYYY-MM-DD
        public readonly ?string $sex,
        public readonly ?string $expiryDate // normalized YYYY-MM-DD
    ) {}
}
