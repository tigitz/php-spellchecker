<?php

declare(strict_types=1);

namespace PhpSpellcheck\Utils;

use PhpSpellcheck\Misspelling;
use PHPUnit\Framework\TestCase;

class IspellParserTest extends TestCase
{
    private const ISPELL_OUTPUT_STUB = <<<OUTPUT
        @(#) International Ispell Version 3.1.20 (but really Aspell 0.60.7-20110707)
        *
        & PHP 12 19: PP, PH
        *

        # customTextProcessor 5

        OUTPUT;

    public function testParseMisspellingsFromOutput()
    {
        /** @var Misspelling $misspellWithSuggestion */
        /** @var Misspelling $misspellWithoutSuggestion */
        [$misspellWithSuggestion, $misspellWithoutSuggestion] = iterator_to_array(IspellParser::parseMisspellingsFromOutput(self::ISPELL_OUTPUT_STUB, $context = ['ctx']));

        $this->assertSame(1, $misspellWithSuggestion->getLineNumber());
        $this->assertSame($context, $misspellWithSuggestion->getContext());
        /**
         * @see IspellParser::adaptInputForTerseModeProcessing()
         */
        $this->assertSame(18, $misspellWithSuggestion->getOffset());
        $this->assertSame(['PP', 'PH'], $misspellWithSuggestion->getSuggestions());
        $this->assertSame('PHP', $misspellWithSuggestion->getWord());

        $this->assertSame(2, $misspellWithoutSuggestion->getLineNumber());
        $this->assertSame($context, $misspellWithoutSuggestion->getContext());
        $this->assertSame(4, $misspellWithoutSuggestion->getOffset());
        $this->assertSame([], $misspellWithoutSuggestion->getSuggestions());
        $this->assertSame('customTextProcessor', $misspellWithoutSuggestion->getWord());
    }

    public function testAdaptInputForTerseModeProcessing()
    {
        $adaptedInput = IspellParser::adaptInputForTerseModeProcessing(
            <<<INPUT

                foo

                *bar

                INPUT
        );

        $this->assertSame(
            <<<EXPECTED
                ^
                ^foo
                ^
                ^*bar

                EXPECTED,
            $adaptedInput
        );
    }
}
