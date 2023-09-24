<?php

declare(strict_types=1);

use PhpSpellcheck\MisspellingFinder;
use PhpSpellcheck\MisspellingHandler\EchoHandler;
use PhpSpellcheck\Spellchecker\Aspell;
use PhpSpellcheck\TextInterface;
use PhpSpellcheck\TextProcessor\TextProcessorInterface;

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

// using a string
$misspellingFinder->find('It\'s_a_mispelling', ['en_US']);
// word: mispelling | line: 1 | offset: 7 | suggestions: mi spelling,mi-spelling,misspelling | context: []
