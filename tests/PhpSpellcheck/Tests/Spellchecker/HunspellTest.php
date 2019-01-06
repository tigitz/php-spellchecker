<?php

declare(strict_types=1);

namespace PhpSpellcheck\Tests\Spellchecker;

use PhpSpellcheck\Exception\ProcessHasErrorOutputException;
use PhpSpellcheck\Misspelling;
use PhpSpellcheck\Spellchecker\Hunspell;
use PhpSpellcheck\Tests\TextTest;
use PhpSpellcheck\Utils\CommandLine;
use PHPUnit\Framework\TestCase;

class HunspellTest extends TestCase
{
    private const FAKE_BINARIES_PATH = [PHP_BINARY, __DIR__ . '/../Fixtures/Hunspell/bin/hunspell.php'];

    public function testSpellcheckFromFakeBinaries()
    {
        $this->assertWorkingSpellcheck(self::FAKE_BINARIES_PATH);
    }

    public function testGetSupportedLanguagesFromFakeBinaries()
    {
        $this->assertWorkingSupportedLanguages(self::FAKE_BINARIES_PATH);
    }

    public function testBadCheckRequest()
    {
        $this->expectException(ProcessHasErrorOutputException::class);
        (new Hunspell(new CommandLine(IspellTest::FAKE_BAD_BINARIES_PATH)))->check('bla');
    }

    /**
     * @group integration
     */
    public function testSpellcheckFromRealBinariesLanguage()
    {
        $hunspell = new Hunspell(new CommandLine(self::realBinaryPath()));
        $misspellings = iterator_to_array($hunspell->check('mispell', ['en_US']));
        $this->assertInstanceOf(Misspelling::class, $misspellings[0]);
    }

    /**
     * @group integration
     */
    public function testSpellcheckFromRealBinaries()
    {
        $this->assertWorkingSpellcheck(self::realBinaryPath());
    }

    /**
     * @group integration
     */
    public function testGetSupportedLanguagesFromRealBinaries()
    {
        $this->assertWorkingSupportedLanguages(self::realBinaryPath());
    }

    public function getTextInput()
    {
        return TextTest::CONTENT_STUB;
    }

    public function getFakeDicts()
    {
        return explode(PHP_EOL, file_get_contents(__DIR__ . '/../Fixtures/Hunspell/dicts.txt'));
    }


    /**
     * @param string|array $binaries
     */
    private function assertWorkingSpellcheck($binaries)
    {
        $hunspell = new Hunspell(new CommandLine($binaries));
        /** @var Misspelling[] $misspellings */
        $misspellings = iterator_to_array($hunspell->check($this->getTextInput(), ['en_US'], ['ctx']));

        $this->assertSame(['ctx'], $misspellings[0]->getContext());
        $this->assertSame('Tigr', $misspellings[0]->getWord());
        $this->assertSame(0, $misspellings[0]->getOffset());
        $this->assertSame(1, $misspellings[0]->getLineNumber());
        $this->assertNotEmpty($misspellings[0]->getSuggestions());

        $this->assertSame(['ctx'], $misspellings[1]->getContext());
        $this->assertSame('страх', $misspellings[1]->getWord());
        $this->assertSame(21, $misspellings[1]->getOffset());
        $this->assertSame(1, $misspellings[1]->getLineNumber());
    }

    /**
     * @param string|array $binaries
     */
    public function assertWorkingSupportedLanguages($binaries)
    {
        $hunspell = new Hunspell(new CommandLine($binaries));
        $this->assertNotFalse(array_search('en_US', $hunspell->getSupportedLanguages()));
    }

    public static function realBinaryPath(): string
    {
        if (getenv('HUNSPELL_BINARY_PATH') === false) {
            throw new \RuntimeException('"HUNSPELL_BINARY_PATH" env must be set to find the executable to run tests on');
        }

        return getenv('HUNSPELL_BINARY_PATH');
    }
}
