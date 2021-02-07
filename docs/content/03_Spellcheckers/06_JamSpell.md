# JamSpell

JamSpell is a modern spellchecking library. It is light-weight, fast and accurate. It considers word surroundings to make better corrections.

## Install
Follow instructions on [https://github.com/bakwc/JamSpell](https://github.com/bakwc/JamSpell)

Or use a dockerized version, you can find the one used for PHP-Spellchecker test here [https://github.com/tigitz/php-spellchecker/tree/jamspell/docker/jamspell](https://github.com/tigitz/php-spellchecker/tree/jamspell/docker/jamspell)

## Usage

### Spellcheck
```php
<?php

// Any object implementing \Psr\Http\Client\ClientInterface
$httpClient = new Psr18Client();

$jamSpell= new JamSpell($httpClient, 'http://jamspell:8080/candidates');

// en_US aspell dictionary is available
$misspellings = $jamSpell->check('mispell', ['en_US'], ['from_example']);
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
$misspellings = $jamSpell->check(new File('path/to/file.txt'), ['en_US'], ['from_file']);
foreach ($misspellings as $misspelling) {
    $misspelling->getWord();
    $misspelling->getLineNumber();
    $misspelling->getOffset();
    $misspelling->getSuggestions();
    $misspelling->getContext();
}
```

### Available dictionaries

Jamspell doesnt provide a way to retrieve available dictionaries for now.

Check the tests for more examples.
