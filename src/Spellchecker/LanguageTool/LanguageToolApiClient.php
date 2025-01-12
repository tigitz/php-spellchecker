<?php

declare(strict_types=1);

namespace PhpSpellcheck\Spellchecker\LanguageTool;

use Psr\Http\Client\ClientInterface;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

class LanguageToolApiClient
{
    private RequestFactoryInterface $requestFactory;

    private StreamFactoryInterface $streamFactory;

    public function __construct(
        private readonly ClientInterface $client,
        private readonly string $baseUrl,
        ?RequestFactoryInterface $requestFactory = null,
        ?StreamFactoryInterface $streamFactory = null
    ) {
        $psr17Factory = new Psr17Factory();
        $this->requestFactory = $requestFactory ?? $psr17Factory;
        $this->streamFactory = $streamFactory ?? $psr17Factory;
    }

    /**
     * @param array<string> $languages
     * @param array<mixed> $options
     *
     * @return array{matches: array<array{
     *   offset: int,
     *   context: array{text: string, offset: int, length: int},
     *   replacements: array<array{value: string}>,
     *   sentence: string,
     *   message: string,
     *   rule: string
     * }>}
     */
    public function spellCheck(string $text, array $languages, array $options): array
    {
        $options['text'] = $text;
        $options['language'] = array_shift($languages);

        if (!empty($languages)) {
            $options['altLanguages'] = implode(',', $languages);
        }

        /** @var array{matches: array<array{offset: int, context: array{text: string, offset: int, length: int}, replacements: array<array{value: string}>, sentence: string, message: string, rule: string}>} */
        return $this->requestAPI(
            '/v2/check',
            'POST',
            [
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Accept' => 'application/json',
            ],
            $options
        );
    }

    /**
     * @return array<string>
     */
    public function getSupportedLanguages(): array
    {
        /** @var array<string> */
        return array_values(array_unique(array_column(
            $this->requestAPI(
                '/v2/languages',
                'GET',
                ['Accept' => 'application/json']
            ),
            'longCode'
        )));
    }

    /**
     * @param array<string, string> $headers
     * @param array<mixed> $queryParams
     *
     * @throws \RuntimeException
     *
     * @return array<mixed>
     */
    public function requestAPI(string $endpoint, string $method, array $headers, array $queryParams = []): array
    {
        $request = $this->requestFactory->createRequest($method, $this->baseUrl . $endpoint);

        foreach ($headers as $name => $value) {
            $request = $request->withHeader($name, $value);
        }

        if (!empty($queryParams)) {
            $stream = $this->streamFactory->createStream(http_build_query($queryParams));
            $request = $request->withBody($stream);
        }

        $response = $this->client->sendRequest($request);

        /** @var array<mixed> $contentAsArray */
        $contentAsArray = \PhpSpellcheck\json_decode((string) $response->getBody(), true);

        return $contentAsArray;
    }
}
