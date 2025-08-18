<?php
declare(strict_types=1);

namespace Laque\Identity\Core;

use Laque\Identity\Contracts\IdentityProviderInterface;
use Laque\Identity\Dto\IdentityQuery;
use Laque\Identity\Dto\MatchScore;
use Psr\Log\LoggerInterface;

final class IdentityService
{
    public function __construct(
        private IdentityProviderInterface $provider,
        private ?LoggerInterface $logger = null
    ) {}

    public function verify(IdentityQuery $q): MatchScore
    {
        $traceId = bin2hex(random_bytes(8));
        $this->logger?->info('identity.verify.start', ['trace_id' => $traceId]);

        // If MRZ is provided, attempt to parse and enrich the query
        if ($q->mrz) {
            try {
                $mrz = MrzParser::parse($q->mrz);
                $this->logger?->info('mrz.parsed', ['trace_id' => $traceId, 'doc' => $mrz->documentNumber]);
            } catch (\Throwable $e) {
                $this->logger?->warning('mrz.parse.error', ['trace_id' => $traceId, 'error' => $e->getMessage()]);
            }
        }

        $record = $this->provider->find($q);
        if (!$record) {
            $this->logger?->info('identity.not_found', ['trace_id' => $traceId]);
            return MatchScore::notFound();
        }

        $score = Scoring::compare($q, $record);
        $this->logger?->info('identity.verify.result', [
            'trace_id' => $traceId,
            'score' => $score->score(),
            'matched' => $score->matched(),
            'reasons' => $score->reasons()
        ]);

        return $score;
    }
}
