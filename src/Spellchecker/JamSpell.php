<?php

declare(strict_types=1);

namespace PhpSpellcheck\Spellchecker;

use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Stream;
use PhpSpellcheck\Exception\RuntimeException;
use PhpSpellcheck\Misspelling;
use PhpSpellcheck\Utils\LineAndOffset;
use Psr\Http\Client\ClientInterface;

class JamSpell implements SpellcheckerInterface
{
    /**
     * @var ClientInterface
     */
    private $httpClient;

    /**
     * @var string
     */
    private $endpoint;

    public function __construct(ClientInterface $httpClient, string $endpoint)
    {
        $this->httpClient = $httpClient;
        $this->endpoint = $endpoint;
    }

    public function check(string $text, array $languages, array $context): iterable
    {
        $request = (new Psr17Factory())
            ->createRequest('POST', $this->endpoint)
            ->withBody(Stream::create($text));

        /** @var array{results: array<array{pos_from: int, len: int, candidates: string[]}>} $spellcheckResponseAsArray */
        $spellcheckResponseAsArray = json_decode($spellcheckResponse = $this->httpClient->sendRequest($request)->getBody()->getContents(), true, flags: JSON_THROW_ON_ERROR);

        if (!isset($spellcheckResponseAsArray['results'])) {
            throw new RuntimeException('Jamspell spellcheck HTTP response must include a "results" key. Response given: "'.$spellcheckResponse.'"');
        }

        foreach ($spellcheckResponseAsArray['results'] as $result) {

            [$line, $offset] = LineAndOffset::findFromFirstCharacterOffset($text, $result['pos_from']);

            yield new Misspelling(
                mb_substr($text, $result['pos_from'], $result['len']),
                $offset,
                $line,
                $result['candidates'],
                $context
            );
        }
    }

    public function getSupportedLanguages(): iterable
    {
        throw new RuntimeException('Jamspell doesn\'t provide a way to retrieve the language its actually supporting through its HTTP API. Rely on the language models it has been setup with.');
    }
}
