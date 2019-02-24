
# Install

Via Composer

```sh
$ composer require tigitz/php-spellcheck
```

# Usage

## Using the Spellchecker directly

You can check misspellings directly from a `PhpSpellCheck\SpellChecker` class and process them on your own.

```php
<?php
use PhpSpellCheck\SpellChecker\Aspell;
// if you made the default aspell installation on you local machine
$aspell = Aspell::create();
// or if you want to use binaries from Docker
$aspell = new Aspell(new CommandLine(['docker','run','--rm', '-i', 'starefossen/aspell']);

$misspellings = $aspell->check('mispell', ['en_US'], ['from_example']);
foreach ($misspellings as $misspelling) {
    $misspelling->getWord(); // 'mispell'
    $misspelling->getLineNumber(); // '1'
    $misspelling->getOffset(); // '0'
    $misspelling->getSuggestions(); // ['misspell', ...]
    $misspelling->getContext(); // ['from_example']
}
```

## Using the MisspellingFinder helper

You can also use an opinionated `MisspellingFinder` class to orchestrate your
spellchecking flow:

<p align="center">
    <img src="https://i.imgur.com/n3JjWgh.png" alt="PHP-Spellcheck-misspellingfinder-flow">
</p>

```php
<?php
use PhpSpellCheck\MisspellingFinder;
use PhpSpellCheck\MisspellingHandler\EchoHandler;
use PhpSpellCheck\SpellChecker\Aspell;
use PhpSpellCheck\TextInterface;
use PhpSpellCheck\TextProcessor\TextProcessorInterface;

// custom text processor that replaces "_" with " "
$customTextProcessor = new class implements TextProcessorInterface {
    public function process(TextInterface $text): TextInterface
    {
        $contentProcessed = str_replace('_', ' ', $text->getContent());
        return $text->replaceContent($contentProcessed);
    }
};

$misspellingFinder = new MisspellingFinder(
    Aspell::create(),
    new EchoHandler(),
    $customTextProcessor
);

$misspellingFinder->find('It\'s_a_mispelling', ['en_US']); // misspellings are echoed
```
