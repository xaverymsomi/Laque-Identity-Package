<?php
declare(strict_types=1);

namespace Laque\Identity\Core;

use Laque\Identity\Contracts\IdentityProviderInterface;
use Laque\Identity\Contracts\CacheInterface;
use Laque\Identity\Dto\IdentityQuery;
use Laque\Identity\Dto\MatchScore;

final class IdentityService
{
	public function __construct(
		private IdentityProviderInterface $provider,
		private ?CacheInterface $cache = null,
		private ?RateLimiter $rateLimiter = null,
		private int $cacheTtlSeconds = 86400
	) {}

	public function verify(IdentityQuery $q): MatchScore
	{
		// Rate limit every call (even if we hit cache)
		if ($this->rateLimiter) {
			$this->rateLimiter->hitOrThrow('identity');
		}

		$cacheKey = 'verify:' . $q->key();

		if ($this->cache && $this->cache->has($cacheKey)) {
			/** @var array<string,mixed> $arr */
			$arr = $this->cache->get($cacheKey);
			return MatchScore::fromArray($arr);
		}

		$record = $this->provider->find($q);
		$score  = $record ? Scoring::compare($q, $record) : MatchScore::notFound();

		if ($this->cache) {
			$this->cache->set($cacheKey, $score->toArray(), $this->cacheTtlSeconds);
		}

		return $score;
	}
}
