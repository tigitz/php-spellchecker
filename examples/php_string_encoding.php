<?php

use PhpSpellcheck\MisspellingFinder;
use PhpSpellcheck\MisspellingHandler\EchoHandler;
use PhpSpellcheck\Spellchecker\Aspell;

require_once __DIR__ . '/../vendor/autoload.php';

$misspellingFinder = new MisspellingFinder(
    Aspell::create(), // Creates aspell spellchecker pointing to "aspell" as it's binary path
    new EchoHandler() // Handles all the misspellings found by echoing their information
);

$misspellings = $misspellingFinder->find(
    $string = new \PhpSpellcheck\Source\PHPString(
        mb_convert_encoding('ça éxagèrre', 'windows-1252'),
        'windows-1252'
    ),
    ['fr_FR'],
    [],
    'iso-8859-1' // Aspell will consider the input text encoded in iso-8859-1 which is roughly equivalent to windows-1252
);
// Output:
// word: �xag�rre | line: 1 | offset: 3 | suggestions: exag�rer,exag�re,exag�r�


/** @var \PhpSpellcheck\Misspelling[]|\Generator $misspellings */
$misspellings = $misspellingFinder->find(
    $string = new \PhpSpellcheck\Source\PHPString('ça éxagèrre'),
    ['fr_FR']
);
// Output:
// word: éxagèrre | line: 1 | offset: 3 | suggestions: exagérer,exagère,exagéré
