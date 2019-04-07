# Create your custom Text Processor

When you want to add a step in your spellchecking flow that will transform
the texts coming out of a source to a desired output, you'll need a class implementing the
[TextProcessorInterface](https://github.com/tigitz/php-spellcheck/blob/master/src/TextProcessor/TextProcessorInterface.php) interface.

Let's say you want to spellcheck filenames that contains `_` as word separators.

You can then create a TextProcessor that will replace the `_` by a space ` ` so
that spellcheckers will understand the input.

```php
<?php

// My custom text processor that replaces "_" by " "
$customTextProcessor = new class implements TextProcessorInterface
{
    public function process(TextInterface $text): TextInterface
    {
        $contentProcessed = str_replace('_', ' ', $text->getContent());

        return $text->replaceContent($contentProcessed);
    }
};

$misspellingFinder = new MisspellingFinder(
    Aspell::create(), // Creates aspell spellchecker pointing to "aspell" as it's binary path
    new EchoHandler(), // Handles all the misspellings found by echoing their information
    $customTextProcessor
);

$misspellingFinder->find('filename_with_a_mispelling', ['en_US']);
// word: mispelling | line: 1 | offset: 7 | suggestions: mi spelling,mi-spelling,misspelling | context: []
```


