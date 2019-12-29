<?php

require __DIR__.'/../vendor/autoload.php';

use PhpSpellcheck\Misspelling;
use PhpSpellcheck\Spellchecker\SpellcheckerInterface;
use PhpSpellcheck\Utils\LineAndOffset;

$phpSpellcheckLibraryNameSpellchecker = new class implements SpellcheckerInterface {
    public function check(
        string $text, array $languages, array $context
    ): iterable {
        foreach (['php-spellchecker', 'php-spellcheckerer', 'php spellchecker'] as $misspelledCandidate) {
            $matches = [];

            if (preg_match('/\b'.$misspelledCandidate.'\b/i', $text, $matches, PREG_OFFSET_CAPTURE) !== false) {
                foreach ($matches as $match) {
                    [$word, $offset] = $match;
                    [$line, $offsetFromLine] = LineAndOffset::findFromFirstCharacterOffset($text, $offset);

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
$misspellings = $phpSpellcheckLibraryNameSpellchecker->check('The PHP-Spellcheckerer library', ['en_US'], ['context']);
foreach ($misspellings as $misspelling) {
    print_r([
        $misspelling->getWord(), // 'PHP-Spellcheckerer'
        $misspelling->getLineNumber(), // '...'
        $misspelling->getOffset(), // '...'
        $misspelling->getSuggestions(), // ['PHP Spellcheck']
        $misspelling->getContext(), // []
    ]);
}
