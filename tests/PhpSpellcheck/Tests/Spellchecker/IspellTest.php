<?php

declare(strict_types=1);

namespace PhpSpellcheck\Tests\Spellchecker;

use PhpSpellcheck\Exception\ProcessHasErrorOutputException;
use PhpSpellcheck\Misspelling;
use PhpSpellcheck\Spellchecker\Ispell;
use PhpSpellcheck\Tests\TextTest;
use PhpSpellcheck\Utils\CommandLine;
use PhpSpellcheck\Utils\TextEncoding;
use PHPUnit\Framework\TestCase;

class IspellTest extends TestCase
{
    public const FAKE_BAD_BINARIES_PATH = __DIR__ . '/../Fixtures/Ispell/bin/empty_output.sh';
    private const FAKE_BINARIES_PATH = __DIR__ . '/../Fixtures/Ispell/bin/ispell.sh';

    public function testSpellcheckFromFakeBinaries()
    {
        $this->assertWorkingSpellcheck(self::FAKE_BINARIES_PATH);
    }

    public function testGetSupportedLanguagesFromFakeBinaries()
    {
        $this->assertWorkingSupportedLanguages(self::FAKE_BINARIES_PATH, self:: FAKE_BINARIES_PATH);
    }

    public function testBadCheckRequest()
    {
        $this->expectException(ProcessHasErrorOutputException::class);
        Ispell::create(self::FAKE_BAD_BINARIES_PATH)->check('bla');
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
        $this->assertWorkingSupportedLanguages(self::realBinaryPath(), self::realShellPath());
    }

    public function getTextInput()
    {
        return TextTest::CONTENT_STUB;
    }

    public function getFakeDicts()
    {
        return explode(PHP_EOL, file_get_contents(__DIR__ . '/../Fixtures/Ispell/dicts.txt'));
    }

    public function assertWorkingSupportedLanguages($binaries, $shellEntryPoint = null)
    {
        $ispell = new Ispell(
            new CommandLine($binaries),
            $shellEntryPoint !== null ? new CommandLine($shellEntryPoint) : null
        );
        $this->assertNotFalse(array_search('american', $ispell->getSupportedLanguages()));
    }

    public static function realBinaryPath(): string
    {
        if (getenv('ISPELL_BINARY_PATH') === false) {
            throw new \RuntimeException('"ISPELL_BINARY_PATH" env must be set to find the executable to run tests on');
        }

        return getenv('ISPELL_BINARY_PATH');
    }

    public static function realShellPath(): ?string
    {
        if (getenv('ISPELL_SHELL_PATH') === false) {
            throw new \RuntimeException('"ISPELL_SHELL_PATH" env must be set to find the executable to run tests on');
        }

        return getenv('ISPELL_SHELL_PATH') ? getenv('ISPELL_SHELL_PATH') : null;
    }

    private function assertWorkingSpellcheck($binaries)
    {
        $ispell = new Ispell(new CommandLine($binaries));
        /** @var Misspelling[] $misspellings */
        $misspellings = iterator_to_array(
            $ispell->check(
                $this->getTextInput(),
                ['american'],
                ['ctx'],
                TextEncoding::UTF8
            )
        );

        $this->assertSame($misspellings[0]->getContext(), ['ctx']);
        $this->assertSame($misspellings[0]->getWord(), 'Tigr');
        $this->assertSame($misspellings[0]->getOffset(), 0);
        $this->assertSame($misspellings[0]->getLineNumber(), 1);
        $this->assertNotEmpty($misspellings[0]->getSuggestions());

        $this->assertSame($misspellings[1]->getContext(), ['ctx']);
        $this->assertSame($misspellings[1]->getWord(), 'theforests');
        $this->assertSame($misspellings[1]->getOffset(), 3);
        $this->assertSame($misspellings[1]->getLineNumber(), 2);
        $this->assertNotEmpty($misspellings[1]->getSuggestions());
    }
}
