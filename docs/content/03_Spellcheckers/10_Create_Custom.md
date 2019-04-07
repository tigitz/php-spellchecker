# Create your custom Spellchecker

A Spellchecker in a PHP-Spellcheck sense, is nothing more than a class that implements
the [Spellchecker interface](https://github.com/tigitz/php-spellcheck/blob/master/src/Spellchecker/SpellcheckerInterface.php).

Let's create a custom spellchecker that checks some possibly misspelled version of the library name.

```php
<?php

$phpSpellcheckLibraryNameSpellchecker = new class implements SpellcheckerInterface {
    public function check(
        string $text,
        array $languages = [],
        array $context = [],
        ?string $encoding = \PhpSpellcheck\Utils\TextEncoding::UTF8
    ): iterable {
        foreach (['php-spellcheck', 'php-spellchecker', 'php spellchecker'] as $misspelledCandidate) {
            $matches = [];
            
            if (preg_match('/\b'.$misspelledCandidate.'\b/i', $text, $matches, PREG_OFFSET_CAPTURE) !== false) {
                foreach ($matches as $match) {
                    [$word, $offset] = $match;
                    [$line, $offsetFromLine] = LineAndOffset::findFromFirstCharacterOffset($text, $offset, $encoding);

                    yield new Misspelling(
                        $word,
                        $offsetFromLine,
                        $line,
                        ['PHP Spellcheck']
                    );
                }
            }
        }
    }

    public function getSupportedLanguages(): iterable
    {
        yield 'en_US';
    }
};

/** @var Misspelling[]|\Generator $misspellings */
$misspellings = $phpSpellcheckLibraryNameSpellchecker->check('The PHP-SpellChecker library', ['en_US']);
foreach ($misspellings as $misspelling) {
    print_r([
        $misspelling->getWord(), // 'PHP-SpellChecker'
        $misspelling->getLineNumber(), // '...'
        $misspelling->getOffset(), // '...'
        $misspelling->getSuggestions(), // ['PHP Spellcheck']
        $misspelling->getContext(), // []
    ]);
}

```
