# LanguageTools

LanguageTool is an Open Source proofreading software for English, French, German,
Polish, Russian, and [more than 20 other languages](https://languagetool.org/languages/).
It finds many errors that a simple spell checker cannot detect.

LanguageTool is freely available under the LGPL 2.1 or later.

For more information, please see homepage at https://languagetool.org,
[this README](https://github.com/languagetool-org/languagetool/blob/master/languagetool-standalone/README.md),
and [CHANGES](https://github.com/languagetool-org/languagetool/blob/master/languagetool-standalone/CHANGES.md).

## Install

PHP-Spellcheck uses the [HTTP API from LanguageTool](http://wiki.languagetool.org/public-http-api).
As such, you'll require to have a proper LanguageTools server installed in order to use this spellchecker. Follow instructions on the [LanguageTools GitHub's repo](https://github.com/languagetool-org/languagetool).

Or you can use docker and start a container of the server exposing the endpoint on port `8011`:
```sh
docker run --rm -d -p 8011:8010 silviof/docker-languagetool
```

## Usage


### Spellcheck
With LanguageTool server available at `http://localhost:8011`
```php
<?php

$spellchecker = new LanguageTool(new LanguageToolApiClient('http://localhost:8011'));

// LanguageTools expects language formatted with a dash `en-US`
$misspellings = $spellchecker->check('mispell', ['en-US'], ['from_example']);
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
$spellchecker = $spellchecker->check(new File('path/to/file.txt'), ['en_US'], ['from_file']);
foreach ($misspellings as $misspelling) {
    $misspelling->getWord();
    $misspelling->getLineNumber();
    $misspelling->getOffset();
    $misspelling->getSuggestions();
    $misspelling->getContext();
}
```

### Available dictionaries

You can check supported languages availability of your LanguageTools installation by running:
```php
<?php

$spellchecker = new LanguageTool(new LanguageToolApiClient('http://localhost:8011'));

$spellchecker->getSupportedLanguages(); // ['en','en-US',...]
```

Check the tests for more examples.
