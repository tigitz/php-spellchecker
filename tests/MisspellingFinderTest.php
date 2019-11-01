<?php

declare(strict_types=1);

namespace PhpSpellcheck\Tests;

use PhpSpellcheck\Exception\InvalidArgumentException;
use PhpSpellcheck\MisspellingFinder;
use PhpSpellcheck\MisspellingHandler\MisspellingHandlerInterface;
use PhpSpellcheck\MisspellingInterface;
use PhpSpellcheck\Source\SourceInterface;
use PhpSpellcheck\Spellchecker\SpellcheckerInterface;
use PhpSpellcheck\TextInterface;
use PhpSpellcheck\TextProcessor\TextProcessorInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class MisspellingFinderTest extends TestCase
{
    /** @var MisspellingHandlerInterface|MockObject */
    private $misspellingHandler;

    /** @var MockObject|TextProcessorInterface */
    private $textProcessor;

    /** @var MockObject|SpellcheckerInterface */
    private $spellChecker;

    public function setUp(): void
    {
        $this->spellChecker = $this->createMock(SpellcheckerInterface::class);
        $this->misspellingHandler = $this->createMock(MisspellingHandlerInterface::class);
        $this->textProcessor = $this->createMock(TextProcessorInterface::class);
    }

    public function testFindFromString(): void
    {
        $misspellingFinder = new MisspellingFinder(
            $this->spellChecker
        );
        $misspelling1 = $this->generateMisspellingMock();

        $this->spellChecker
            ->expects($this->once())
            ->method('check')
            ->willReturn([$misspelling1]);

        $this->assertSame([$misspelling1], iterator_to_array($misspellingFinder->find('mispell')));
    }

    public function testFindFromInvalidArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $misspellingFinder = new MisspellingFinder(
            $this->spellChecker,
            $this->misspellingHandler,
            $this->textProcessor
        );

        $misspellingFinder->find(3);
    }

    public function testFindWithMisspelingHandler(): void
    {
        $this->misspellingHandler->expects($this->once())
            ->method('handle');

        $misspellingFinder = new MisspellingFinder(
            $this->spellChecker,
            $this->misspellingHandler
        );
        $misspelling1 = $this->generateMisspellingMock();

        $this->spellChecker
            ->expects($this->once())
            ->method('check')
            ->willReturn([$misspelling1]);

        $this->assertSame([$misspelling1], iterator_to_array($misspellingFinder->find('mispell')));
    }

    public function testFindWithTextProcessor(): void
    {
        $this->textProcessor->expects($this->once())
            ->method('process');

        $misspellingFinder = new MisspellingFinder(
            $this->spellChecker,
            null,
            $this->textProcessor
        );
        $misspelling1 = $this->generateMisspellingMock();

        $this->spellChecker
            ->expects($this->once())
            ->method('check')
            ->willReturn([$misspelling1]);

        $this->assertSame([$misspelling1], iterator_to_array($misspellingFinder->find('mispell')));
    }

    public function testFindFromSource(): void
    {
        $misspelling1 = $this->generateMisspellingMock();
        $this->spellChecker
            ->expects($this->once())
            ->method('check')
            ->willReturn([$misspelling1]);

        $source = $this->createMock(SourceInterface::class);
        $source->method('toTexts')
            ->willReturn([$text = $this->generateTextMock()]);

        $misspellingFinder = new MisspellingFinder(
            $this->spellChecker,
            null,
            $this->textProcessor
        );

        $this->assertSame([$misspelling1], iterator_to_array($misspellingFinder->find('mispell')));
    }

    private function generateMisspellingMock(): MockObject
    {
        $mispelling = $this->createMock(MisspellingInterface::class);
        $mispelling->method('getWord')
            ->willReturn('mispelled');
        $mispelling->method('getContext')
            ->willReturn([]);
        $mispelling->method('getLineNumber')
            ->willReturn(1);
        $mispelling->method('getOffset')
            ->willReturn(1);
        $mispelling->method('getSuggestions')
            ->willReturn(['misspelled', 'misspelling']);

        return $mispelling;
    }

    private function generateTextMock(): MockObject
    {
        $text = $this->createMock(TextInterface::class);
        $text->method('getContent')
            ->willReturn('mispell');
        $text->method('getContext')
            ->willReturn([]);
        $text->method('getEncoding')
            ->willReturn('utf-8');

        return $text;
    }
}
