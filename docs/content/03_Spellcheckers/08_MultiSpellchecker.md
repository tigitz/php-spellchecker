# MultiSpellchecker

One of the benefit of an unified spellchecking interface is that you can define your spellchecker to act like a chain of different spellcheckers execution.

And this what the `MultiSpellchecker` is.

Say you want the combination of `LanguageTools` and `Hunspell` spellchecker for **MAXIMUM SPELLCHECKING!!**

You just need to define a `MultiSpellchecker` like the following:

```php
<?php

// Create your spellcheckers
$languagetools = new LanguageTool(new LanguageToolApiClient('http://localhost:8011'));
$hunspell = Hunspell::create();

// by default, merge the misspells (suggestions and context)
$multiSpellchecker = new MultiSpellchecker([$languagetools, $hunspell]); 

// or keep duplicates
$multiSpellcheckerWithDuplicates = new MultiSpellchecker([$languagetools, $hunspell], false); 

/** @var \PhpSpellcheck\Misspelling[]|\Generator $misspellings */
$misspellings = $multiSpellchecker->check('mispell', ['en-US','en_US'], ['from' => 'multispellchecker']);
foreach ($misspellings as $misspelling) {
    print_r([
        $misspelling->getWord(), // 'mispell'
        $misspelling->getLineNumber(), // '1'
        $misspelling->getOffset(), // '0'
        $misspelling->getSuggestions(), // ['misspell', ...]
        $misspelling->getContext(), // ['from' => 'multispellchecker']
    ]);
}
```

