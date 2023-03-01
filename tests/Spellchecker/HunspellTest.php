<?php

declare(strict_types=1);

use PhpSpellcheck\Exception\ProcessHasErrorOutputException;
use PhpSpellcheck\Misspelling;
use PhpSpellcheck\Spellchecker\Hunspell;
use PhpSpellcheck\Tests\TextTest;
use PhpSpellcheck\Utils\CommandLine;
use PHPUnit\Framework\TestCase;

class HunspellTest extends TestCase
{
    private const FAKE_BINARIES_PATH = [PHP_BINARY, __DIR__ . '/../Fixtures/Hunspell/bin/hunspell.php'];

    public function testSpellcheckFromFakeBinaries(): void
    {
        $this->assertWorkingSpellcheckENText(self::FAKE_BINARIES_PATH, IspellTest::CONTENT_STUB, ['en_US']);
    }

    public function testGetSupportedLanguagesFromFakeBinaries(): void
    {
        $this->assertWorkingSupportedLanguages(self::FAKE_BINARIES_PATH);
    }

    public function testBadCheckRequest(): void
    {
        $this->expectException(ProcessHasErrorOutputException::class);
        (new Hunspell(new CommandLine(IspellTest::FAKE_BAD_BINARIES_PATH)))->check('foo');
    }

    /**
     * @group integration
     */
    public function testSpellcheckFromRealBinariesLanguage(): void
    {
        $hunspell = new Hunspell(new CommandLine(self::realBinaryPath()));
        $misspellings = iterator_to_array($hunspell->check('mispell', ['en_US']));
        $this->assertInstanceOf(Misspelling::class, $misspellings[0]);
    }

    /**
     * @group integration
     */
    public function testSpellcheckFromRealBinaries(): void
    {
        $this->assertWorkingSpellcheckENText(self::realBinaryPath(), IspellTest::CONTENT_STUB, ['en_US']);
    }

    /**
     * @group integration
     */
    public function testSpellcheckFromRealBinariesUTF8(): void
    {
        $this->assertWorkingSpellcheckRUText(self::realBinaryPath(), TextTest::CONTENT_STUB_RU, ['ru_RU']);
    }

    /**
     * @group integration
     */
    public function testGetSupportedLanguagesFromRealBinaries(): void
    {
        $this->assertWorkingSupportedLanguages(self::realBinaryPath());
    }

    public function getFakeDicts(): array
    {
        return explode(PHP_EOL, \Safe\file_get_contents(__DIR__ . '/../Fixtures/Hunspell/dicts.txt'));
    }

    /**
     * @param array|string $binaries
     */
    public function assertWorkingSupportedLanguages($binaries): void
    {
        $hunspell = new Hunspell(new CommandLine($binaries));
        $languages = \is_array($hunspell->getSupportedLanguages()) ? $hunspell->getSupportedLanguages() : iterator_to_array($hunspell->getSupportedLanguages());
        $this->assertNotFalse(array_search('en_US', $languages, true));
    }

    public static function realBinaryPath(): string
    {
        if (getenv('HUNSPELL_BINARY_PATH') === false) {
            throw new \RuntimeException('"HUNSPELL_BINARY_PATH" env must be set to find the executable to run tests on');
        }

        return getenv('HUNSPELL_BINARY_PATH');
    }

    /**
     * @param array|string $binaries
     */
    private function assertWorkingSpellcheckENText($binaries, string $textInput, array $locales): void
    {
        $hunspell = new Hunspell(new CommandLine($binaries));
        /** @var Misspelling[] $misspellings */
        $misspellings = iterator_to_array($hunspell->check($textInput, $locales, ['ctx']));

        $this->assertSame(['ctx'], $misspellings[0]->getContext());
        $this->assertSame('Tigr', $misspellings[0]->getWord());
        $this->assertSame(0, $misspellings[0]->getOffset());
        $this->assertSame(1, $misspellings[0]->getLineNumber());
        $this->assertNotEmpty($misspellings[0]->getSuggestions());

        $this->assertSame(['ctx'], $misspellings[1]->getContext());
        $this->assertSame('страх', $misspellings[1]->getWord());
        $this->assertSame(21, $misspellings[1]->getOffset());
        $this->assertSame(1, $misspellings[1]->getLineNumber());

        $this->assertSame(['ctx'], $misspellings[6]->getContext());
        $this->assertSame('mispell', $misspellings[6]->getWord());
        $this->assertSame(1, $misspellings[6]->getOffset());
        $this->assertSame(6, $misspellings[6]->getLineNumber());
        $this->assertNotEmpty($misspellings[6]->getSuggestions());
    }

    /**
     * @param array|string $binaries
     */
    private function assertWorkingSpellcheckRUText($binaries, string $textInput, array $locales): void
    {
        $hunspell = new Hunspell(new CommandLine($binaries));
        /** @var Misspelling[] $misspellings */
        $misspellings = iterator_to_array($hunspell->check($textInput, $locales, ['ctx']));

        $this->assertSame(['ctx'], $misspellings[0]->getContext());
        $this->assertSame('граматических', $misspellings[0]->getWord());
        $this->assertSame(1, $misspellings[0]->getLineNumber());
        $this->assertSame(54, $misspellings[0]->getOffset());

        $this->assertSame(['ctx'], $misspellings[1]->getContext());
        $this->assertSame('англиских', $misspellings[1]->getWord());
        $this->assertSame(1, $misspellings[1]->getLineNumber());
        $this->assertSame(94, $misspellings[1]->getOffset());
    }
}
