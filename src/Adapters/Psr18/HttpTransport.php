<?php
declare(strict_types=1);

namespace Laque\Identity\Adapters\Psr18;

use Laque\Identity\Contracts\TransportInterface;
use Laque\Identity\Security\HmacSigner;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

final class HttpTransport implements TransportInterface
{
    public function __construct(
        private string $baseUrl,
        private string $apiKey,
        private ?ClientInterface $client = null,
        private ?RequestFactoryInterface $requestFactory = null,
        private ?StreamFactoryInterface $streamFactory = null,
        private int $timeout = 10,
        private string $signerSecret = ''
    ) {}

    /** @param array<string,mixed> $payload */
    public function post(string $uri, array $payload, array $headers = []): array
    {
        if (!$this->client || !$this->requestFactory || !$this->streamFactory) {
            throw new \RuntimeException('PSR-18 client and factories must be provided');
        }

        $url = rtrim($this->baseUrl, '/') . '/' . ltrim($uri, '/');
        $body = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $request = $this->requestFactory->createRequest('POST', $url)
            ->withHeader('Content-Type', 'application/json')
            ->withHeader('Accept', 'application/json')
            ->withHeader('Authorization', 'Bearer ' . $this->apiKey);

        if ($this->signerSecret !== '' && $body !== false) {
            $sig = HmacSigner::sign($body, $this->signerSecret);
            $request = $request->withHeader('X-Signature', $sig);
        }

        foreach ($headers as $k => $v) {
            $request = $request->withHeader((string)$k, (string)$v);
        }

        $request = $request->withBody($this->streamFactory->createStream($body ?: '{}'));

        $response = $this->client->sendRequest($request);
        $status = $response->getStatusCode();
        $respBody = (string)$response->getBody();

        if ($status >= 400) {
            throw new \RuntimeException('HTTP error ' . $status . ': ' . $respBody);
        }

        $decoded = json_decode($respBody, true);
        if (!is_array($decoded)) {
            throw new \RuntimeException('Invalid JSON response');
        }
        return $decoded;
    }
}
