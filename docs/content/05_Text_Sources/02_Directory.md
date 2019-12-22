# Directory

`Directory` is a simple source that iterates over a folder and generates
`Text` classes from every file it founds in this directory. You can use a regex
as a second argument to filter on filename.

```php
<?php

use PhpSpellcheck\Spellchecker\Aspell;

// *** Using spellcheckers directly ***
$aspell = Aspell::create(); // Creates aspell spellchecker pointing to "aspell" as it's binary path

/** @var \PhpSpellcheck\Misspelling[]|\Generator $misspellings */
$misspellings = $aspell->check(
    new \PhpSpellcheck\Source\Directory('/my/path/to/directory', '/regexpattern/'), ['en_US'],
);

foreach ($misspellings as $misspelling) {
    print_r([
        $misspelling->getWord(), // 'mispell'
        $misspelling->getLineNumber(), // '1'
        $misspelling->getOffset(), // '0'
        $misspelling->getSuggestions(), // ['misspell', ...]
        $misspelling->getContext(), // []
    ]);
}
```
