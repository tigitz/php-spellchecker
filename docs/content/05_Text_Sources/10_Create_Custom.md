# Create your custom Text Source

You can create your own source of `Text` object by instantiating a class
that simply implements the [SourceInterface](https://github.com/tigitz/php-spellchecker/blob/master/src/Source/SourceInterface.php).

Let's create an in memory source that will return two `Text` objects.

```php
<?php

$misspellingFinder = new MisspellingFinder(
    Aspell::create(), // Creates aspell spellchecker pointing to "aspell" as it's binary path
    new EchoHandler() // Handles all the misspellings found by echoing their information
);

// Using a custom SourceInterface that generates two Text objects
$inMemoryTextProvider = new class implements SourceInterface
{
    public function toTexts(array $context): iterable
    {
        yield new Text('my_mispell', $context + ['from_source_interface']);
        yield new Text('my_other_mispell', $context + ['from_named_constructor']);
    }
};

$misspellingFinder->find($inMemoryTextProvider, ['en_US']);
//word: mispell | line: 1 | offset: 3 | suggestions: mi spell,mi-spell,misspell,... | context: ["from_source_interface"]
//word: mispell | line: 1 | offset: 9 | suggestions: mi spell,mi-spell,misspell,... | context: ["from_named_constructor"]

```
