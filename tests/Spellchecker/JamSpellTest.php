<?php

declare(strict_types=1);

namespace PhpSpellcheck\Tests\Spellchecker;

use PhpSpellcheck\Exception\RuntimeException;
use PhpSpellcheck\Spellchecker\JamSpell;
use PhpSpellcheck\Tests\TextTest;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\NativeHttpClient;
use Symfony\Component\HttpClient\Psr18Client;

class JamSpellTest extends TestCase
{
    /**
     * @group integration
     */
    public function testSpellcheckFromRealAPI(): void
    {
        $misspellings = iterator_to_array(
            $this->realJamSpellClient()->check(
                TextTest::CONTENT_STUB,
                ['ja-JP'],
                ['ctx' => 'ctx']
            )
        );

        $this->assertArrayHasKey('ctx', $misspellings[0]->getContext());
        $this->assertSame($misspellings[0]->getWord(), 'Tigr');
        $this->assertSame($misspellings[0]->getOffset(), 0);
        $this->assertSame($misspellings[0]->getLineNumber(), 1);
        $this->assertNotEmpty($misspellings[0]->getSuggestions());
    }

    public function testSupportedLanguages(): void
    {
        $this->expectException(RuntimeException::class);
        $this->realJamSpellClient()->getSupportedLanguages();
    }

    private function realJamSpellClient(): JamSpell
    {
        return new JamSpell(new Psr18Client(new NativeHttpClient()), $this->realAPIEndpoint().'/candidates');
    }

    private function realAPIEndpoint(): string
    {
        if (getenv('JAMSPELL_ENDPOINT') === false) {
            throw new \RuntimeException('"JAMSPELL_ENDPOINT" env must be set to run the tests on');
        }

        return getenv('JAMSPELL_ENDPOINT');
    }
}
