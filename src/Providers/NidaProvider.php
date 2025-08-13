<?php
declare(strict_types=1);

namespace Laque\Identity\Providers;

use Laque\Identity\Contracts\IdentityProviderInterface;
use Laque\Identity\Contracts\TransportInterface;
use Laque\Identity\Dto\IdentityQuery;
use Laque\Identity\Dto\IdentityRecord;

final class NidaProvider implements IdentityProviderInterface
{
    public function __construct(
        private TransportInterface $transport,
        private string $endpoint,
        private string $apiKey,
        private int $timeoutSeconds = 10
    ) {}

    public function find(IdentityQuery $q): ?IdentityRecord
    {
        if (!$q->nin) {
            return null;
        }
        $payload = ['nin' => $q->nin];
        $headers = ['Authorization' => 'Bearer ' . $this->apiKey, 'Content-Type' => 'application/json'];

        try {
            $json = $this->transport->postJson($this->endpoint, $payload, $headers, $this->timeoutSeconds);
        } catch (\Throwable $e) {
            return null;
        }

        // Expected JSON keys (example): fullName, dob, phone, nin, tin
        if (!is_array($json) || !isset($json['nin'])) {
            return null;
        }

        return new IdentityRecord(
            $json['fullName'] ?? '',
            new \DateTimeImmutable((string)($json['dob'] ?? '1970-01-01')),
            $json['phone'] ?? null,
            $json['nin'] ?? null,
            $json['tin'] ?? null,
            $json
        );
    }
}
