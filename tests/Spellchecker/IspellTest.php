<?php

declare(strict_types=1);

use PhpSpellcheck\Exception\ProcessHasErrorOutputException;
use PhpSpellcheck\Misspelling;
use PhpSpellcheck\Spellchecker\Ispell;
use PhpSpellcheck\Tests\TextTest;
use PhpSpellcheck\Utils\CommandLine;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Group;

class IspellTest extends TestCase
{
    public const CONTENT_STUB = TextTest::CONTENT_STUB.PHP_EOL.PHP_EOL.'*mispell';

    public const FAKE_BAD_BINARIES_PATH = __DIR__ . '/../Fixtures/Ispell/bin/empty_output.sh';
    private const FAKE_BINARIES_PATH = __DIR__ . '/../Fixtures/Ispell/bin/ispell.sh';

    public function testSpellcheckFromFakeBinaries(): void
    {
        $this->assertWorkingSpellcheck(self::FAKE_BINARIES_PATH);
    }

    public function testGetSupportedLanguagesFromFakeBinaries(): void
    {
        $this->assertWorkingSupportedLanguages(self::FAKE_BINARIES_PATH, self::FAKE_BINARIES_PATH);
    }

    public function testBadCheckRequest(): void
    {
        $this->expectException(ProcessHasErrorOutputException::class);
        Ispell::create(self::FAKE_BAD_BINARIES_PATH)->check('bla');
    }

    #[Group('integration')]
    public function testSpellcheckFromRealBinaries(): void
    {
        $this->assertWorkingSpellcheck(self::realBinaryPath());
    }

    #[Group('integration')]
    public function testGetSupportedLanguagesFromRealBinaries(): void
    {
        $this->assertWorkingSupportedLanguages(self::realBinaryPath(), self::realShellPath());
    }

    public function getTextInput(): string
    {
        return self::CONTENT_STUB;
    }

    public function getFakeDicts(): array
    {
        return explode(PHP_EOL, \PhpSpellcheck\file_get_contents(__DIR__ . '/../Fixtures/Ispell/dicts.txt'));
    }

    public function assertWorkingSupportedLanguages(string $binaries, ?string $shellEntryPoint = null): void
    {
        $ispell = new Ispell(
            new CommandLine($binaries),
            $shellEntryPoint !== null ? new CommandLine($shellEntryPoint) : null
        );

        $languages = \is_array($ispell->getSupportedLanguages()) ? $ispell->getSupportedLanguages() : iterator_to_array($ispell->getSupportedLanguages());
        $this->assertNotFalse(array_search('american', $languages, true));
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

        return getenv('ISPELL_SHELL_PATH') ?: null;
    }

    private function assertWorkingSpellcheck(string $binaries): void
    {
        $ispell = new Ispell(new CommandLine($binaries));
        /** @var Misspelling[] $misspellings */
        $misspellings = iterator_to_array(
            $ispell->check(
                $this->getTextInput(),
                ['american'],
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
}
