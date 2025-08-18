<?php
declare(strict_types=1);

namespace Laque\Identity\Dto;

final class IdentityQuery
{
    public function __construct(
        public readonly ?string $nidaNumber = null,
        public readonly ?string $dateOfBirth = null, // YYYY-MM-DD
        public readonly ?string $firstName = null,
        public readonly ?string $lastName = null,
        public readonly ?string $phone = null,
        public readonly ?string $tin = null,
        public readonly ?string $mrz = null
    ) {}
}
