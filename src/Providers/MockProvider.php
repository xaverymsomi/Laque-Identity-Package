<?php
declare(strict_types=1);

namespace Laque\Identity\Providers;

use Laque\Identity\Contracts\IdentityProviderInterface;
use Laque\Identity\Dto\IdentityQuery;
use Laque\Identity\Dto\IdentityRecord;

final class MockProvider implements IdentityProviderInterface
{
    public function find(IdentityQuery $q): ?IdentityRecord
    {
        // Return a deterministic mock for testing
        $first = $q->firstName ?? 'ANNA';
        $last  = $q->lastName ?? 'ERIKSSON';
        $dob   = $q->dateOfBirth ?? '1974-08-12';
        return new IdentityRecord($first, $last, $dob, gender: 'F', nationality: 'TZA', phone: $q->phone);
    }
}
