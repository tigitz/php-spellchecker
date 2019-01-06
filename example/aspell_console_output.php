<?php

use PhpSpellCheck\MisspellingFinder;
use PhpSpellCheck\MisspellingHandler\EchoHandler;
use PhpSpellCheck\SpellChecker\Aspell;
use PhpSpellCheck\TextInterface;
use PhpSpellCheck\TextProcessor\TextProcessorInterface;

require_once __DIR__ . '/../vendor/autoload.php';

// *** Using spellcheckers directly ***
$aspell = Aspell::create(); // Creates aspell spellchecker pointing to "aspell" as it's binary path
/** @var \PhpSpellCheck\Misspelling[] $misspellings */
$misspellings = $aspell->check('mispell', ['en_US']); // $misspellings is a \Generator here
foreach ($misspellings as $misspelling) {
    $misspelling->getWord();
    $misspelling->getLineNumber();
    $misspelling->getOffset();
    $misspelling->getSuggestions();
    $misspelling->getContext();
}

// *** Using MisspellingFinder class to orchestrate spellchecking flow  ***
// My custom text processor that replaces "_" by " "
$customTextProcessor = new class implements TextProcessorInterface
{
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

$misspellingFinder->find('It\'s_a_mispelling', ['en_US']); // Misspellings are echoed
