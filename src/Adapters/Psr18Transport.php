<?php
declare(strict_types=1);

namespace Laque\Identity\Adapters;

use Laque\Identity\Contracts\TransportInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

final class Psr18Transport implements TransportInterface
{
    public function __construct(
        private ClientInterface $client,
        private RequestFactoryInterface $requestFactory,
        private StreamFactoryInterface $streamFactory
    ) {}

    public function postJson(string $url, array $payload, array $headers = [], int $timeoutSeconds = 10): array
    {
        $body = json_encode($payload, JSON_UNESCAPED_SLASHES);
        $request = $this->requestFactory->createRequest('POST', $url)
            ->withHeader('Content-Type', 'application/json');

        foreach ($headers as $k => $v) {
            $request = $request->withHeader($k, $v);
        }

        $request = $request->withBody($this->streamFactory->createStream($body ?? ''));
        $response = $this->client->sendRequest($request);
        $contents = (string) $response->getBody();
        /** @var array<string, mixed>|null $decoded */
        $decoded = json_decode($contents, true);

        return is_array($decoded) ? $decoded : [];
    }
}
