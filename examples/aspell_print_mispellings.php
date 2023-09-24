<?php

declare(strict_types=1);

use PhpSpellcheck\Spellchecker\Aspell;

require_once __DIR__ . '/../vendor/autoload.php';

// *** Using spellcheckers directly ***
$aspell = Aspell::create(); // Creates aspell spellchecker pointing to "aspell" as it's binary path

/** @var \Generator|\PhpSpellcheck\Misspelling[] $misspellings */
$misspellings = $aspell->check('mispell', ['en_US'], ['from' => 'aspell spellchecker']);
foreach ($misspellings as $misspelling) {
    print_r([
        $misspelling->getWord(), // 'mispell'
        $misspelling->getLineNumber(), // '1'
        $misspelling->getOffset(), // '0'
        $misspelling->getSuggestions(), // ['misspell', ...]
        $misspelling->getContext(), // ['from' => 'aspell spellchecker']
    ]);
}
