<?php
declare(strict_types=1);

namespace Laque\Identity\Contracts;

use Laque\Identity\Dto\IdentityQuery;
use Laque\Identity\Dto\IdentityRecord;

interface IdentityProviderInterface
{
    public function find(IdentityQuery $q): ?IdentityRecord;
}
