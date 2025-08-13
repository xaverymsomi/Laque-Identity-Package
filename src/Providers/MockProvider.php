<?php
declare(strict_types=1);

namespace Laque\Identity\Providers;

use Laque\Identity\Contracts\IdentityProviderInterface;
use Laque\Identity\Dto\IdentityQuery;
use Laque\Identity\Dto\IdentityRecord;

final class MockProvider implements IdentityProviderInterface
{
    /** @var array<int, array<string, string>> */
    private array $data = [
        [
            'nin' => '19990101999999999999',
            'fullName' => 'Jane Doe',
            'dob' => '1999-01-01',
            'phone' => '0712345678',
            'tin' => '123456789',
        ],
        [
            'nin' => '19851205999999999999',
            'fullName' => 'John Smith',
            'dob' => '1985-12-05',
            'phone' => '0751234567',
            'tin' => '987654321',
        ],
    ];

    public function find(IdentityQuery $q): ?IdentityRecord
    {
        foreach ($this->data as $row) {
            if ($q->nin && $row['nin'] === $q->nin) {
                return new IdentityRecord(
                    $row['fullName'],
                    new \DateTimeImmutable($row['dob']),
                    $row['phone'],
                    $row['nin'],
                    $row['tin']
                );
            }
        }

        // fallback by name + dob
        if ($q->fullName && $q->dateOfBirth) {
            foreach ($this->data as $row) {
                if (mb_strtolower($row['fullName']) === mb_strtolower($q->fullName)
                    && $row['dob'] === $q->dateOfBirth->format('Y-m-d')) {
                    return new IdentityRecord(
                        $row['fullName'],
                        new \DateTimeImmutable($row['dob']),
                        $row['phone'],
                        $row['nin'],
                        $row['tin']
                    );
                }
            }
        }
        return null;
    }
}
