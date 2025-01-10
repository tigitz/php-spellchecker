<?php

declare(strict_types=1);

use PhpSpellcheck\Exception\ProcessHasErrorOutputException;
use PhpSpellcheck\Misspelling;
use PhpSpellcheck\Spellchecker\Aspell;
use PhpSpellcheck\Tests\TextTest;
use PhpSpellcheck\Utils\CommandLine;
use PHPUnit\Framework\TestCase;

class AspellTest extends TestCase
{
    private const FAKE_BINARIES_PATH = __DIR__ . '/../Fixtures/Aspell/bin/aspell.sh';

    public function testSpellcheckFromFakeBinaries(): void
    {
        $this->assertWorkingSpellcheck(self::FAKE_BINARIES_PATH);
    }

    public function testBadCheckRequest(): void
    {
        $this->expectException(ProcessHasErrorOutputException::class);
        Aspell::create(IspellTest::FAKE_BAD_BINARIES_PATH)->check('bla');
    }

    public function testGetSupportedLanguagesFromFakeBinaries(): void
    {
        $this->assertWorkingSupportedLanguages(self::FAKE_BINARIES_PATH);
    }

    /**
     * @group integration
     */
    public function testSpellcheckFromRealBinaries(): void
    {
        $this->assertWorkingSpellcheck(self::realBinaryPath());
    }

    /**
     * @group integration
     */
    public function testSpellcheckFromRealBinariesUTF8(): void
    {
        $this->assertWorkingSpellcheckRUText(self::realBinaryPath());
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
        return explode(PHP_EOL, \Safe\file_get_contents(__DIR__ . '/../Fixtures/Aspell/dicts.txt'));
    }

    public function assertWorkingSupportedLanguages(string $binaries): void
    {
        $aspell = new Aspell(new CommandLine($binaries));
        $languages = \is_array($aspell->getSupportedLanguages()) ? $aspell->getSupportedLanguages() : iterator_to_array($aspell->getSupportedLanguages());
        $this->assertNotFalse(array_search('en_GB', $languages, true));
    }

    public static function realBinaryPath(): string
    {
        if (getenv('ASPELL_BINARY_PATH') === false) {
            throw new \RuntimeException('"ASPELL_BINARY_PATH" env must be set to find the executable to run tests on');
        }

        return getenv('ASPELL_BINARY_PATH');
    }

    private function assertWorkingSpellcheck(string $binaries): void
    {
        $aspell = new Aspell(new CommandLine($binaries));
        /** @var Misspelling[] $misspellings */
        $misspellings = iterator_to_array(
            $aspell->check(
                IspellTest::CONTENT_STUB,
                ['en_US'],
                ['ctx']
            )
        );

        $this->assertSame(['ctx'], $misspellings[0]->getContext());
        $this->assertSame('Tigr', $misspellings[0]->getWord());
        $this->assertSame(0, $misspellings[0]->getOffset());
        $this->assertSame(1, $misspellings[0]->getLineNumber());
        $this->assertNotEmpty($misspellings[0]->getSuggestions());

        $this->assertSame(['ctx'], $misspellings[1]->getContext());
        $this->assertSame('theforests', $misspellings[1]->getWord());
        $this->assertSame(3, $misspellings[1]->getOffset());
        $this->assertSame(2, $misspellings[1]->getLineNumber());
        $this->assertNotEmpty($misspellings[1]->getSuggestions());

        $this->assertSame(['ctx'], $misspellings[5]->getContext());
        $this->assertSame('mispell', $misspellings[5]->getWord());
        $this->assertSame(1, $misspellings[5]->getOffset());
        $this->assertSame(6, $misspellings[5]->getLineNumber());
        $this->assertNotEmpty($misspellings[5]->getSuggestions());
    }

    /**
     * @param array|string $binaries
     */
    private function assertWorkingSpellcheckRUText($binaries): void
    {
        $aspell = new Aspell(new CommandLine($binaries));
        /** @var Misspelling[] $misspellings */
        $misspellings = iterator_to_array($aspell->check(TextTest::CONTENT_STUB_RU, ['ru'], ['ctx']));

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