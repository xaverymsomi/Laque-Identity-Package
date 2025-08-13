<?php
declare(strict_types=1);

namespace Laque\Identity\Providers;

use Laque\Identity\Contracts\IdentityProviderInterface;
use Laque\Identity\Dto\IdentityQuery;
use Laque\Identity\Dto\IdentityRecord;

final class CsvProvider implements IdentityProviderInterface
{
    public function __construct(private string $csvPath) {}

    public function find(IdentityQuery $q): ?IdentityRecord
    {
        if (!is_file($this->csvPath)) {
            return null;
        }
        if (($h = fopen($this->csvPath, 'r')) === false) {
            return null;
        }
        $header = fgetcsv($h);
        while (($row = fgetcsv($h)) !== false) {
            $rec = array_combine($header, $row);
            if (!$rec) continue;
            if ($q->nin && ($rec['nin'] ?? null) === $q->nin) {
                fclose($h);
                return new IdentityRecord(
                    (string)$rec['fullName'],
                    new \DateTimeImmutable((string)$rec['dob']),
                    $rec['phone'] ?? null,
                    $rec['nin'] ?? null,
                    $rec['tin'] ?? null,
                    $rec
                );
            }
        }
        fclose($h);
        return null;
    }
}
