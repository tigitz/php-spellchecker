<?php

declare(strict_types=1);

namespace PhpSpellcheck\Spellchecker\LanguageTool;

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
     * @throws \RuntimeException
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

        return \Safe\json_decode($content, true);
    }
}
