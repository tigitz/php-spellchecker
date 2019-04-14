# PHP Pspell

The purpose of **Pspell** (**P**ortable **Spell** Checker Interface Library) was to provide a generic interface to the system [spelling checking](/wiki/Spelling_checker "Spelling checker") libraries.

[PHP’s Pspell extension](http://php.net/manual/en/book.pspell.php), while retaining its current name, now uses the Aspell library.

## Install
PHP PSpell extension installation is lacking documentation so your best bet is probably a [google search](https://www.google.com/search?q=install+pspell+php&ie=utf-8&oe=utf-8) for your OS.


## Usage
Now that PHP PSpell extension is installed on your system, let's see how to spellcheck a word using **PHP-Spellchecker** and **PHP PSpell extension**.

### Spellcheck
```php
<?php

$phpPspell = new PHPPspell();

// en_US aspell dictionary is available
$misspellings = $phpPspell->check('mispell', ['en_US'], ['from_example']);
foreach ($misspellings as $misspelling) {
    $misspelling->getWord(); // 'mispell'
    $misspelling->getLineNumber(); // '1'
    $misspelling->getOffset(); // '0'
    $misspelling->getSuggestions(); // ['misspell', ...]
    $misspelling->getContext(); // ['from_example']
}
```
Or if you want to check a file instead:
```php
<?php
// spellchecking a file
$misspellings = $phpPspell->check(new File('path/to/file.txt'), ['en_US'], ['from_file']);
foreach ($misspellings as $misspelling) {
    $misspelling->getWord();
    $misspelling->getLineNumber();
    $misspelling->getOffset();
    $misspelling->getSuggestions();
    $misspelling->getContext();
}
```

### Available dictionaries

Not implemented yet.

Check the tests for more examples.
