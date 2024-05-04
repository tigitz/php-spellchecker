<?php

declare(strict_types=1);

use PhpSpellcheck\MisspellingFinder;
use PhpSpellcheck\MisspellingHandler\EchoHandler;
use PhpSpellcheck\Spellchecker\Aspell;

require_once __DIR__ . '/../vendor/autoload.php';

$misspellingFinder = new MisspellingFinder(
    Aspell::create(), // Creates aspell spellchecker pointing to "aspell" as it's binary path
    new EchoHandler() // Handles all the misspellings found by echoing their information
);
/** @var \Generator|\PhpSpellcheck\Misspelling[] $misspellings */
$misspellings = $misspellingFinder->find(
    new \PhpSpellcheck\Source\MultiSource(
        [
            new \PhpSpellcheck\Source\File(__DIR__ . '/../tests/Fixtures/Text/mispelling1.txt'),
            new \PhpSpellcheck\Source\Directory(__DIR__ . '/../tests/Fixtures/Text/Directory'),
        ]
    ),
    ['en_US'],
    ['from' => 'aspell spellchecker']
);
