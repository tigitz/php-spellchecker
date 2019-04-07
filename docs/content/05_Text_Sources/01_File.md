# File

The `File` source lets you point to a specific file path retrievable by the
 `file_get_contents` function and generates a `Text` class from it.

```php
<?php

use PhpSpellcheck\Spellchecker\Aspell;

// *** Using spellcheckers directly ***
$aspell = Aspell::create(); // Creates aspell spellchecker pointing to "aspell" as it's binary path

/** @var \PhpSpellcheck\Misspelling[]|\Generator $misspellings */
$misspellings = $aspell->check(
    new \PhpSpellcheck\Source\File('/my/path/to/text.txt'),
    ['en_US'],
    ['from' => 'aspell spellchecker']
);

foreach ($misspellings as $misspelling) {
    print_r([
        $misspelling->getWord(), // 'mispell'
        $misspelling->getLineNumber(), // '1'
        $misspelling->getOffset(), // '0'
        $misspelling->getSuggestions(), // ['misspell', ...]
        $misspelling->getContext(), // ['from' => 'aspell spellchecker']
    ]);
}
```
