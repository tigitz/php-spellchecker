# Aspell

GNU Aspell, usually called just Aspell, is a free software spell checker designed to replace Ispell. It is the standard spell checker for the GNU operating system. It also compiles for other Unix-like operating systems and Windows.
Dictionaries for it are available for about 70 languages.

## Install
GNU Aspell is available in the default repositories of most Linux distributions, so installation won’t be a big deal.

On **Arch Linux** and derivatives like **Antergos**, **Manjaro Linux**, run:

```sh
$ sudo pacman -S aspell
```
On **Fedora**:
```sh
$ sudo dnf install aspell
```
On **RHEL**, **CentOS**:
```sh
$ sudo yum install epel-release

$ sudo yum install aspell
```
On **Debian**, **Ubuntu**:
```sh
$ sudo apt-get install aspell
```

on **Mac OSX** with **Homebrew**:
```sh
brew install aspell
```
By default, Aspell won’t have any dictionaries. To add a dictionary, for example English, just install this package – aspell-en. Similarly, to add Spanish dictionary, install aspell-es package.

This can also be found in the default repositories. For instance, to add English dictionary on Arch linux, run:
```sh
$ sudo pacman -S aspell-en
```
On **Debian**, **Ubuntu**:
```sh
$ sudo apt-get install aspell-en
```
On **Fedora**:
```sh
$ sudo dnf install aspell-en
```
On **RHEL**/**CentOS**:
```sh
$ sudo yum install aspell-en
```

Once installed all dictionaries, you can ensure whether the required dictionaries are available or not using command:
```sh
$ aspell dicts
en
en-variant_0
en-variant_1
...
```
## Usage
Now that you have the english dictionary installed in your system, let's see how to spellcheck a word using **PHP-Spellchecker** and **Aspell**.

### Spellcheck
```php
<?php
// if you made the default aspell installation on you local machine
$aspell = Aspell::create();

// or if you want to use binaries from Docker
$aspell = new Aspell(new CommandLine(['docker','run','--rm', '-i', 'starefossen/aspell']);

// en_US aspell dictionary is available
$misspellings = $aspell->check('mispell', ['en_US'], ['from_example']);
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
$misspellings = $aspell->check(new File('path/to/file.txt'), ['en_US'], ['from_file']);
foreach ($misspellings as $misspelling) {
    $misspelling->getWord();
    $misspelling->getLineNumber();
    $misspelling->getOffset();
    $misspelling->getSuggestions();
    $misspelling->getContext();
}
```

### Available dictionaries

You can check current aspell installation languages availability  before trying to spellcheck with an unsupported language for example.
```php
<?php
// if you made the default aspell installation on you local machine
$aspell = Aspell::create();

$aspell->getSupportedLanguages(); // ['en','en_US',...]
```

Check the tests for more examples.

