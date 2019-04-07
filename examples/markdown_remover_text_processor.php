<?php

use PhpSpellcheck\MisspellingFinder;
use PhpSpellcheck\MisspellingHandler\EchoHandler;
use PhpSpellcheck\Spellchecker\Aspell;
use PhpSpellcheck\TextInterface;
use PhpSpellcheck\TextProcessor\MarkdownRemover;
use PhpSpellcheck\TextProcessor\TextProcessorInterface;

require_once __DIR__ . '/../vendor/autoload.php';


$misspellingFinder = new MisspellingFinder(
    Aspell::create(), // Creates aspell spellchecker pointing to "aspell" as it's binary path
    new EchoHandler(), // Handles all the misspellings found by echoing their information
    new MarkdownRemover()
);

$mdFormattedString = <<<MD
# Mispelling Heading

**mispelling bold**

* mispelling list item
MD;

// using a string
$misspellingFinder->find($mdFormattedString, ['en_US']);
// word: mispelling | line: 1 | offset: 7 | suggestions: mi spelling,mi-spelling,misspelling | context: []

