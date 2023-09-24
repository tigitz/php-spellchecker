<?php

declare(strict_types=1);

use PhpSpellcheck\MisspellingFinder;
use PhpSpellcheck\MisspellingHandler\EchoHandler;
use PhpSpellcheck\Source\SourceInterface;
use PhpSpellcheck\Spellchecker\Aspell;
use PhpSpellcheck\TextInterface;
use PhpSpellcheck\TextProcessor\TextProcessorInterface;

use function PhpSpellcheck\t;

require_once __DIR__ . '/../vendor/autoload.php';

// My custom text processor that replaces "_" by " "
$customTextProcessor = new class () implements TextProcessorInterface {
    public function process(TextInterface $text): TextInterface
    {
        $contentProcessed = str_replace('_', ' ', $text->getContent());

        return $text->replaceContent($contentProcessed);
    }
};

$misspellingFinder = new MisspellingFinder(
    Aspell::create(), // Creates aspell spellchecker pointing to "aspell" as it's binary path
    new EchoHandler(), // Handles all the misspellings found by echoing their information
    $customTextProcessor
);

// Using a custom SourceInterface that generates Text
$inMemoryTextProvider = new class () implements SourceInterface {
    public function toTexts(array $context): iterable
    {
        yield t('my_mispell', ['from_source_interface']);
        yield t('my_other_mispell', ['from_named_constructor']);
    }
};

$misspellingFinder->find($inMemoryTextProvider, ['en_US']);
//word: mispell | line: 1 | offset: 3 | suggestions: mi spell,mi-spell,misspell,... | context: ["from_source_interface"]
//word: mispell | line: 1 | offset: 9 | suggestions: mi spell,mi-spell,misspell,... | context: ["from_named_constructor"]
