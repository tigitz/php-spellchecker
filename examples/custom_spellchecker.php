<?php

require __DIR__.'/../vendor/autoload.php';

use PhpSpellcheck\Misspelling;
use PhpSpellcheck\Spellchecker\SpellcheckerInterface;

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

                    // here you're supposed to compute the misspelled word offset from the preceding line break offset
                    // and also retrieve the line number
                    $lineOffset = null;
                    $lineNumber = null;

                    yield new Misspelling(
                        $word,
                        $lineOffset,
                        $lineNumber,
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
