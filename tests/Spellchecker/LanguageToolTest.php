<?php

declare(strict_types=1);

use Nyholm\Psr7\Factory\Psr17Factory;
use PhpSpellcheck\Misspelling;
use PhpSpellcheck\Spellchecker\LanguageTool;
use PhpSpellcheck\Spellchecker\LanguageTool\LanguageToolApiClient;
use PhpSpellcheck\Tests\TextTest;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Group;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Symfony\Component\HttpClient\Psr18Client;

class LanguageToolTest extends TestCase
{
    public function testSpellcheck(): void
    {
        [$httpClient, $requestFactory, $streamFactory, $request, $response, $stream] = $this->setupMocks();

        $request->expects($this->exactly(2))
            ->method('withHeader')
            ->willReturnSelf();

        $requestFactory->expects($this->once())
            ->method('createRequest')
            ->willReturn($request);

        $streamFactory->expects($this->once())
            ->method('createStream')
            ->willReturn($stream);

        $request->expects($this->once())
            ->method('withBody')
            ->with($stream)
            ->willReturnSelf();

        $response->expects($this->once())
            ->method('getBody')
            ->willReturn($stream);

        $httpClient->expects($this->once())
            ->method('sendRequest')
            ->with($request)
            ->willReturn($response);

        $stream->expects($this->once())
            ->method('__toString')
            ->willReturn(json_encode([
                'matches' => [
                    [
                        'message' => 'Possible spelling mistake found',
                        'replacements' => [['value' => 'Tier']],
                        'offset' => 0,
                        'length' => 4,
                        'context' => [
                            'text' => 'Tigr, tiger, burning страх. In theforests of...',
                            'offset' => 0,
                            'length' => 4,
                        ],
                        'sentence' => 'Tigr, tiger, burning страх.',
                        'rule' => [
                            'description' => 'Possible spelling mistake',
                            'issueType' => 'misspelling',
                        ],
                    ],
                    [
                        'message' => 'Possible spelling mistake found',
                        'replacements' => [['value' => 'Could']],
                        'offset' => 81,
                        'length' => 6,
                        'context' => [
                            'text' => '... of the night, What imortal hand or eey CCould frame thy fearful symmetry?',
                            'offset' => 43,
                            'length' => 6,
                        ],
                        'sentence' => "In theforests of the night,\nWhat imortal hand or eey\nCCould frame thy fearful symmetry?",
                        'rule' => [
                            'description' => 'Possible spelling mistake',
                            'issueType' => 'misspelling',
                        ],
                    ],
                ],
            ]));

        $client = new LanguageToolApiClient($httpClient, 'http://example.com', $requestFactory, $streamFactory);
        $this->assertWorkingSpellcheck($client);
    }

    public function testGetSupportedLanguages(): void
    {
        [$httpClient, $requestFactory, $streamFactory , $request, $response, $stream] = $this->setupMocks();

        $request->expects($this->once())
            ->method('withHeader')
            ->willReturnSelf();

        $requestFactory->expects($this->once())
            ->method('createRequest')
            ->willReturn($request);

        $httpClient->expects($this->once())
            ->method('sendRequest')
            ->with($request)
            ->willReturn($response);

        $response->expects($this->once())
            ->method('getBody')
            ->willReturn($stream);

        $stream->expects($this->once())
            ->method('__toString')
            ->willReturn(json_encode([['longCode' => 'en']]));

        $client = new LanguageToolApiClient($httpClient, 'http://example.com', $requestFactory, $streamFactory);
        $this->assertWorkingSupportedLanguages($client);
    }

    #[Group('integration')]
    public function testSpellcheckFromRealAPI(): void
    {
        $psr17Factory = new Psr17Factory();
        $this->assertWorkingSpellcheck(new LanguageToolApiClient(
            new Psr18Client(),
            self::realAPIEndpoint(),
            $psr17Factory,
            $psr17Factory
        ));
    }

    #[Group('integration')]
    public function testSpellcheckMultiBytesStringFromRealAPI(): void
    {
        $psr17Factory = new Psr17Factory();
        $misspellings = iterator_to_array(
            (new LanguageTool(new LanguageToolApiClient(
                new Psr18Client(),
                self::realAPIEndpoint(),
                $psr17Factory,
                $psr17Factory
            )))->check(
                TextTest::CONTENT_STUB_JP,
                ['ja-JP'],
                ['ctx' => 'ctx']
            )
        );

        $this->assertArrayHasKey('ctx', $misspellings[0]->getContext());
        $this->assertSame($misspellings[0]->getWord(), '解決なる');
        $this->assertSame($misspellings[0]->getOffset(), 4);
        $this->assertSame($misspellings[0]->getLineNumber(), 1);
        $this->assertNotEmpty($misspellings[0]->getSuggestions());

        $this->assertArrayHasKey('ctx', $misspellings[1]->getContext());
        $this->assertSame($misspellings[1]->getWord(), '解決なる');
        $this->assertSame($misspellings[1]->getOffset(), 0);
        $this->assertSame($misspellings[1]->getLineNumber(), 5);
        $this->assertNotEmpty($misspellings[1]->getSuggestions());
    }

    #[Group('integration')]
    public function testGetSupportedLanguagesFromRealBinaries(): void
    {
        $psr17Factory = new Psr17Factory();
        $this->assertWorkingSupportedLanguages(new LanguageToolApiClient(
            new Psr18Client(),
            self::realAPIEndpoint(),
            $psr17Factory,
            $psr17Factory
        ));
    }

    public function getTextInput(): string
    {
        return TextTest::CONTENT_STUB;
    }

    public function assertWorkingSupportedLanguages(LanguageToolApiClient $apiClient): void
    {
        $languageTool = new LanguageTool($apiClient);
        $this->assertNotFalse(array_search('en', $languageTool->getSupportedLanguages(), true));
    }

    public static function realAPIEndpoint(): string
    {
        if (getenv('LANGUAGETOOLS_ENDPOINT') === false) {
            throw new \RuntimeException('"LANGUAGETOOLS_ENDPOINT" env must be set to run the tests on');
        }

        return getenv('LANGUAGETOOLS_ENDPOINT');
    }

    /**
     * @return array{
     *   ClientInterface,
     *   RequestFactoryInterface,
     *   StreamFactoryInterface,
     *   RequestInterface,
     *   ResponseInterface,
     *   StreamInterface
     * }
     */
    private function setupMocks(): array
    {
        return [
            $this->createMock(ClientInterface::class),
            $this->createMock(RequestFactoryInterface::class),
            $this->createMock(StreamFactoryInterface::class),
            $this->createMock(RequestInterface::class),
            $this->createMock(ResponseInterface::class),
            $this->createMock(StreamInterface::class),
        ];
    }

    private function assertWorkingSpellcheck(LanguageToolApiClient $apiClient): void
    {
        $languageTool = new LanguageTool($apiClient);
        /** @var Misspelling[] $misspellings */
        $misspellings = iterator_to_array(
            $languageTool->check(
                $this->getTextInput(),
                ['en-US'],
                ['ctx' => 'ctx']
            )
        );

        // test first line offset computation
        $this->assertArrayHasKey('ctx', $misspellings[0]->getContext());
        $this->assertSame($misspellings[0]->getWord(), 'Tigr');
        $this->assertSame($misspellings[0]->getOffset(), 0);
        $this->assertSame($misspellings[0]->getLineNumber(), 1);
        $this->assertNotEmpty($misspellings[0]->getSuggestions());

        end($misspellings);
        $lastKey = key($misspellings);
        // test last line offset computation
        $this->assertArrayHasKey('ctx', $misspellings[$lastKey]->getContext());
        $this->assertSame($misspellings[$lastKey]->getWord(), 'CCould');
        $this->assertSame($misspellings[$lastKey]->getOffset(), 0);
        $this->assertSame($misspellings[$lastKey]->getLineNumber(), 4);
        $this->assertNotEmpty($misspellings[$lastKey]->getSuggestions());
    }
}
