<?php

declare(strict_types=1);

use PhpSpellcheck\Misspelling;
use PhpSpellcheck\Spellchecker\MultiSpellchecker;
use PhpSpellcheck\Spellchecker\SpellcheckerInterface;
use PHPUnit\Framework\TestCase;

class MultiSpellcheckerTest extends TestCase
{
    public function testCheckAndMergeSuggestions(): void
    {
        $spellChecker1 = $this->createMock(SpellcheckerInterface::class);
        $spellChecker2 = $this->createMock(SpellcheckerInterface::class);

        $misspelling1 = new Misspelling('mispelling1', 1);
        $misspelling2a = new Misspelling('mispelling2', 2, 2, ['suggestionA']);
        $misspelling2b = new Misspelling('mispelling2', 2, 2, ['suggestionB']);
        $misspelling3 = new Misspelling('mispelling3', 3, 3);

        $spellChecker1->method('check')
            ->willReturn([$misspelling1, $misspelling2a]);
        $spellChecker2->method('check')
            ->willReturn([$misspelling2b, $misspelling3]);
        $spellChecker1->method('getSupportedLanguages')
            ->willReturn(['en_US']);
        $spellChecker2->method('getSupportedLanguages')
            ->willReturn(['en_US']);

        $multiSpellchecker = new MultiSpellchecker([$spellChecker1, $spellChecker2]);

        $misspellings = iterator_to_array($multiSpellchecker->check('test', ['en_US'], ['ctx']));
        $this->assertEquals(
            [
                $misspelling1->setContext(['ctx']),
                new Misspelling('mispelling2', 2, 2, ['suggestionA', 'suggestionB'], ['ctx']),
                $misspelling3->setContext(['ctx']),
            ],
            $misspellings
        );
    }

    public function testCheckAndNotMergeSuggestions(): void
    {
        $spellChecker1 = $this->createMock(SpellcheckerInterface::class);
        $spellChecker2 = $this->createMock(SpellcheckerInterface::class);

        $misspelling1 = new Misspelling('mispelling1', 1);
        $misspelling2a = new Misspelling('mispelling2', 2, 2, ['suggestionA']);
        $misspelling2b = new Misspelling('mispelling2', 2, 2, ['suggestionB']);
        $misspelling3 = new Misspelling('mispelling3', 3, 3);

        $spellChecker1->method('check')
            ->willReturn([$misspelling1, $misspelling2a]);
        $spellChecker2->method('check')
            ->willReturn([$misspelling2b, $misspelling3]);
        $spellChecker1->method('getSupportedLanguages')
            ->willReturn(['en_US']);
        $spellChecker2->method('getSupportedLanguages')
            ->willReturn(['en_US']);

        $multiSpellchecker = new MultiSpellchecker([$spellChecker1, $spellChecker2], false);

        $misspellings = iterator_to_array($multiSpellchecker->check('test', ['en_US'], ['ctx']));
        $this->assertEquals(
            [
                $misspelling1->setContext(['ctx']),
                $misspelling2a->setContext(['ctx']),
                $misspelling2b->setContext(['ctx']),
                $misspelling3->setContext(['ctx']),
            ],
            $misspellings
        );
    }

    public function testGetSupportedLanguages(): void
    {
        $spellChecker1 = $this->createMock(SpellcheckerInterface::class);
        $spellChecker1->method('getSupportedLanguages')
            ->willReturn(['en', 'fr']);
        $spellChecker2 = $this->createMock(SpellcheckerInterface::class);
        $spellChecker2->method('getSupportedLanguages')
            ->willReturn(['fr', 'ru']);

        $multipleSpellchecker = new MultiSpellchecker([$spellChecker1, $spellChecker2]);

        $this->assertSame(['en', 'fr', 'ru'], $multipleSpellchecker->getSupportedLanguages());
    }
}
