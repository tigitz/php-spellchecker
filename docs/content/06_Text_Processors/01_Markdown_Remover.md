# Markdown Remover

`MarkdownRemover` text processor aims at removing all the markdown tags
of a text so that only the actual words can be checked.

This is useful when you want to spellcheck an entire ebook or documentation
written in markdown format.

**It's in an experimental stage right now and doesn't provide accurate offset and line numbers.**

```php
<?php

$misspellingFinder = new MisspellingFinder(
    Aspell::create(), // Creates aspell spellchecker pointing to "aspell" as it's binary path
    new EchoHandler(), // Handles all the misspellings found by echoing their information
    new MarkdownRemover()
);

$mdFormattedString = <<<MD
# Mispelling Heading

**mispelling bold**

* mispelling list item
MD;

// using a string
$misspellingFinder->find($mdFormattedString, ['en_US']);
// word: Mispelling | line: 1 | offset: 0 | suggestions: Mi spelling,Mi-spelling,Misspelling,Dispelling,Misspellings,Spelling,Miscalling,Misdealing,Respelling,Misspelling's | context: []
// word: mispelling | line: 2 | offset: 0 | suggestions: mi spelling,mi-spelling,misspelling,dispelling,misspellings,spelling,miscalling,misdealing,respelling,misspelling's | context: []
// word: mispelling | line: 3 | offset: 0 | suggestions: mi spelling,mi-spelling,misspelling,dispelling,misspellings,spelling,miscalling,misdealing,respelling,misspelling's | context: []
```
