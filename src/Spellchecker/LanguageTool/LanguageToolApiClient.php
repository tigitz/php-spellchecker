<?php

declare(strict_types=1);

namespace PhpSpellcheck\Spellchecker\LanguageTool;

/**
 * @TODO refactor by using PSR HTTP Client
 */
class LanguageToolApiClient
{
    /**
     * @var string
     */
    private $baseUrl;

    public function __construct(string $baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    /**
     * @param array<string> $languages
     * @param array<mixed> $options
     *
     * @return array<string, mixed>
     */
    public function spellCheck(string $text, array $languages, array $options): array
    {
        $options['text'] = $text;
        $options['language'] = array_shift($languages);

        if (!empty($languages)) {
            $options['altLanguages'] = implode(',', $languages);
        }

        return $this->requestAPI(
            '/v2/check',
            'POST',
            'Content-type: application/x-www-form-urlencoded; Accept: application/json',
            $options
        );
    }

    /**
     * @return array<string>
     */
    public function getSupportedLanguages(): array
    {
        return array_column(
            $this->requestAPI(
                '/v2/languages',
                'GET',
                'Accept: application/json'
            ),
            'longCode'
        );
    }

    /**
     * @param array<mixed> $queryParams
     *
     * @throws \RuntimeException
     *
     * @return array<mixed>
     */
    public function requestAPI(string $endpoint, string $method, string $header, array $queryParams = []): array
    {
        $httpData = [
            'method' => $method,
            'header' => $header,
        ];

        if (!empty($queryParams)) {
            $httpData['content'] = http_build_query($queryParams);
        }

        $content = \Safe\file_get_contents($this->baseUrl . $endpoint, false, stream_context_create(['http' => $httpData]));
        /** @var array<mixed> $contentAsArray */
        $contentAsArray = \Safe\json_decode($content, true);

        return $contentAsArray;
    }
}
