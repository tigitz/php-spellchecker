# Hunspell

Hunspell is the spell checker of [LibreOffice](http://www.libreoffice.org/), [OpenOffice.org](http://www.openoffice.org/), [Mozilla Firefox 3 & Thunderbird](http://www.mozilla.com/en-US), Google Chrome, and it is also used by proprietary software packages, like macOS, InDesign, memoQ, Opera and SDL Trados.

Main features:

*   Extended support for language peculiarities; Unicode character encoding, compounding and complex morphology.
*   Improved suggestion using n-gram similarity, rule and dictionary based pronunciation data.
*   Morphological analysis, stemming and generation.
*   Hunspell is based on [MySpell](http://lingucomponent.openoffice.org/MySpell-3.zip) and works also with MySpell dictionaries.
*   C++ library under GPL/LGPL/MPL tri-license.

## Install
Hunspell is available in the default repositories of most Linux distributions, so installation won’t be a big deal.

On **Arch Linux** and derivatives like **Antergos**, **Manjaro Linux**, run:

```sh
$ sudo pacman -S hunspell
```
On **Fedora**:
```sh
$ sudo dnf install hunspell
```
On **RHEL**, **CentOS**:
```sh
$ sudo yum install epel-release

$ sudo yum install hunspell
```
On **Debian**, **Ubuntu**:
```sh
$ sudo apt-get install hunspell
```

on **Mac OSX** with **Homebrew**:
```sh
brew install hunspell
```
By default, Hunspell won’t have any dictionaries. To add a dictionary, for example English, just install this package – `hunspell-en-us`. Similarly, to add Spanish dictionary, install `hunspell-es` package.

This can also be found in the default repositories. For instance, to add English dictionary on Arch linux, run:
```sh
$ sudo pacman -S hunspell-en-us
```

On **Debian**, **Ubuntu**:
```sh
$ sudo apt-get install hunspell-en-us
```
On **Fedora**:
```sh
$ sudo dnf install hunspell-en-us
```
On **RHEL**/**CentOS**:
```sh
$ sudo yum install hunspell-en-us
```

on **Mac OSX**:

Download dictionaries from http://wordlist.aspell.net/dicts/ and put them to `/Library/Spelling/`.

Once installed all dictionaries, you can ensure whether the required dictionaries are available or not using command:
```sh
$ hunspell -D
/usr/share/hunspell/en_US
...
```
## Usage
Now that you have the english dictionary installed in your system, let's see how to spellcheck a word using **PHP-Spellchecker** and **Hunspell**.

### Spellcheck
```php
<?php
// if you made the default hunspell installation on you local machine
$hunspell = Hunspell::create();

// or if you want to use binaries from Docker
$hunspell = new Hunspell(new CommandLine(['docker','run','--rm', '-i', 'tmaier/hunspell']);

// en_US hunspell dictionary is available
$misspellings = $hunspell->check('mispell', ['en_US'], ['from_example']);
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
$misspellings = $hunspell->check(new File('path/to/file.txt'), ['en_US'], ['from_file']);
foreach ($misspellings as $misspelling) {
    $misspelling->getWord();
    $misspelling->getLineNumber();
    $misspelling->getOffset();
    $misspelling->getSuggestions();
    $misspelling->getContext();
}
```

### Available dictionaries

You can check current hunspell installation languages availability before trying to spellcheck with an unsupported language for example.
```php
<?php

$hunspell = Hunspell::create();

$hunspell->getSupportedLanguages(); // ['en','en_US',...]
```

Check the tests for more examples.
