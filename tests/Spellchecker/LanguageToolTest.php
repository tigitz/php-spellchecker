<?php

declare(strict_types=1);

use PhpSpellcheck\Misspelling;
use PhpSpellcheck\Spellchecker\LanguageTool;
use PhpSpellcheck\Spellchecker\LanguageTool\LanguageToolApiClient;
use PhpSpellcheck\Tests\TextTest;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Group;

class LanguageToolTest extends TestCase
{
    /**
     * @return LanguageToolApiClient|MockObject
     */
    public function getClientMock(): MockObject
    {
        $mock = $this->getMockBuilder(LanguageToolApiClient::class)
            ->disableOriginalConstructor()
            ->getMock();

        return $mock;
    }

    public function testSpellcheck(): void
    {
        $client = $this->getClientMock();
        $client->expects($this->once())
            ->method('spellCheck')
            ->willReturn([
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
            ]);

        $this->assertWorkingSpellcheck($client);
    }

    public function testGetSupportedLanguages(): void
    {
        $client = $this->getClientMock();
        $client->expects($this->once())
            ->method('getSupportedLanguages')
            ->willReturn(['en']);

        $this->assertWorkingSupportedLanguages($client);
    }

    #[Group('integration')]
    public function testSpellcheckFromRealAPI(): void
    {
        $this->assertWorkingSpellcheck(new LanguageToolApiClient(self::realAPIEndpoint()));
    }

    #[Group('integration')]
    public function testSpellcheckMultiBytesStringFromRealAPI(): void
    {
        $misspellings = iterator_to_array(
            (new LanguageTool(new LanguageToolApiClient(self::realAPIEndpoint())))->check(
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
        $this->assertWorkingSpellcheck(new LanguageToolApiClient(self::realAPIEndpoint()));
    }

    #[Group('integration')]
    public function testGetSupportedLanguagesFromRealBinaries(): void
    {
        $this->assertWorkingSupportedLanguages(new LanguageToolApiClient(self::realAPIEndpoint()));
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
