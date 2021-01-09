<?php

namespace PhpSpellcheck\Tests\Spellchecker;

use PhpSpellcheck\Spellchecker\Jamspell;
use PhpSpellcheck\Tests\TextTest;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\Psr18Client;

class JamspellTest extends TestCase
{
    /**
     * @group integration
     */
    public function testSpellcheckMultiBytesStringFromRealAPI(): void
    {
        $misspellings = iterator_to_array(
            (new Jamspell(new Psr18Client(), 'http://localhost:5466/candidates'))->check(
                TextTest::CONTENT_STUB,
                ['ja-JP'],
                ['ctx' => 'ctx']
            )
        );

        $this->assertArrayHasKey('ctx', $misspellings[1]->getContext());
        $this->assertSame($misspellings[1]->getWord(), 'Tigr');
        $this->assertSame($misspellings[1]->getOffset(), 0);
        $this->assertSame($misspellings[1]->getLineNumber(), 5);
        $this->assertNotEmpty($misspellings[1]->getSuggestions());
    }
}
