<?php

declare(strict_types=1);

namespace PhpSpellcheck\Utils;

use PhpSpellcheck\Misspelling;
use PHPUnit\Framework\TestCase;

class IspellOutputParserTest extends TestCase
{
    private const ISPELL_OUTPUT_STUB = <<<OUTPUT
@(#) International Ispell Version 3.1.20 (but really Aspell 0.60.7-20110707)
*
& PHP 12 19: PP, PH
*

# customTextProcessor 5

OUTPUT;

    public function testParseMisspellings()
    {
        /** @var Misspelling $misspellWithSuggestion */
        /** @var Misspelling $misspellWithoutSuggestion */
        [$misspellWithSuggestion, $misspellWithoutSuggestion] = iterator_to_array(IspellOutputParser::parseMisspellings(self::ISPELL_OUTPUT_STUB, $context = ['ctx']));

        $this->assertSame(1, $misspellWithSuggestion->getLineNumber());
        $this->assertSame($context, $misspellWithSuggestion->getContext());
        $this->assertSame(19, $misspellWithSuggestion->getOffset());
        $this->assertSame(['PP', 'PH'], $misspellWithSuggestion->getSuggestions());
        $this->assertSame('PHP', $misspellWithSuggestion->getWord());

        $this->assertSame(2, $misspellWithoutSuggestion->getLineNumber());
        $this->assertSame($context, $misspellWithoutSuggestion->getContext());
        $this->assertSame(5, $misspellWithoutSuggestion->getOffset());
        $this->assertSame([], $misspellWithoutSuggestion->getSuggestions());
        $this->assertSame('customTextProcessor', $misspellWithoutSuggestion->getWord());
    }
}
