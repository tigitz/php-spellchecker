<h1 align="center">PHP-Spellcheck</h1>

<p align="center">
	<img src="https://i.imgur.com/C8hHwW9.png" alt="PHP-Spellcheck" width="300" height="300">
</p>
<p align="center">
	<a href="https://travis-ci.org/tigitz/php-spellchecker"><img src="https://img.shields.io/travis/com/tigitz/php-spellchecker/master.svg?style=flat-square&logo=travis" alt="Build Status"></a>
	<a href="https://scrutinizer-ci.com/g/tigitz/php-spellchecker/?branch=master"><img src="https://img.shields.io/scrutinizer/coverage/g/tigitz/php-spellchecker/master.svg?style=flat-square&logo=scrutinizer" alt="Code coverage"></a>
	<a href="https://scrutinizer-ci.com/g/tigitz/php-spellchecker/?branch=master"><img src="https://img.shields.io/scrutinizer/g/tigitz/php-spellchecker.svg?style=flat-square&logo=scrutinizer" alt="Code coverage"></a>
	<a href="https://gitter.im/php-spellchecker/community"><img src="https://img.shields.io/gitter/room/tigitz/php-spellchecker.svg?style=flat-square" alt="PHP-Spellcheck chat room"></a>
	<a href="https://choosealicense.com/licenses/mit/"><img src="https://img.shields.io/github/license/tigitz/php-spellchecker.svg?style=flat-square" alt="License"></a>
</p>

<p align="center">Check misspellings from any text sources by the most popular spellcheckers available, directly from PHP.</p>


------
## Features

- 🧐 Support Spellcheckers: [Aspell][aspell], [Hunspell][hunspell], [Ispell][ispell], [PHP Pspell][pspell], [LanguageTools][languagetools] and [MultiSpellchecker](src/Spellchecker/MultiSpellchecker.php) [(contribute yours!)](docs/spellchecker/create-custom.md)
- 📄 Support Text Sources: Filesystem [File](src/Source/File.php)/[Directory](src/Source/Directory.php), [String](src/Source/PHPString.php), and [Multisource](src/Source/MultipleSource.php) [(contribute yours!)](docs/spellchecker/create-custom.md)
- 🛠 Support Text Processors: [MarkdownRemover](src/TextProcessor/MarkdownRemover.php) [(contribute yours!)](docs/text-proccesor/create-custom.md)
- 🔁 Support Misspelling Handlers: [EchoHandler](src/MisspellingHandler/EchoHandler.php) [(contribute yours!)](docs/misspellings-handler/create-custom.md)
- ➰ Make use of generators to lower memory footprint
- ⚖ Flexible and straight forward design
- 💡 Really easy to implement your own spellcheckers, text processors and misspellings handlers
- 💪 Tests are run against real spellcheckers to ensure full compatibility

**PHP-Spellcheck** is a really welcoming project for any new contributors.

Want
to make **your first open-source contribution** ? Check the [Roadmap](#roadmap),
pick one task, [open an issue](https://github.com/tigitz/php-spellchecker/issues/new) and we'll help you go through it 🤓🚀.

## Install

Via Composer

``` bash
$ composer require tigitz/php-spellcheck
```

## Usage

### Spellchecker directly
You can check misspellings directly from a `PhpSpellCheck\SpellChecker` class and process
them on your own.

```php
<?php
use PhpSpellCheck\SpellChecker\Aspell;
// if you made the default aspell installation on you local machine
$aspell = Aspell::create();
// or if you want to use binaries from docker
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

### MisspellingFinder helper
Or you can use an opinionated `MisspellingFinder` class to orchestrate your
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

// My custom text processor that replaces "_" by " "
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

$misspellingFinder->find('It\'s_a_mispelling', ['en_US']); // Misspellings are echoed
```

## Roadmap

### Global
- [ ] Add a cli that could do something like `vendor/bin/php-spellcheck "mispell" Languagetools EchoHandler --lang=en_US`
- [ ] Add asynchronous mechanism to spellcheckers
- [ ] Make some computed misspelling properties optional to enhance performance for certain use cases (Lines and offset in `LanguageTools` spellchecker for example)
- [ ] Add a languages mapper to manage their different representations among spellcheckers
- [ ] Evaluate the use of `strtok` function to parse lines of text instead of `explode` for performance
- [ ] Evaluate the use of a `MutableMisspelling` for performance comparison
- [ ] Wrap `Webmozart/Assert` library exceptions to throw PHP-Spellcheck own exceptions
- [ ] Improve the `Makefile`

### Source
- [ ] Make `SourceInterface` class able to have an effect on the spellchecker configuration used
- [ ] League/Flysystem plugin
- [ ] Symfony/Finder plugin

### Text Processor
- [ ] Markdown - find a way to keep original offset and line of words after stripping
- [ ] Add PHPDoc processor

### SpellCheck
- [ ] Cache suggestions of already spellchecked words (PSR 6 / PSR 16 ?)
- [ ] Pspell - find way to compute word offset
- [ ] LanguageTools - Evaluate the use of http-plug library to make api request
- [ ] Pspell - find way to list available dictionaries
- [ ] Add JamSpell spellchecker
- [ ] Add NuSpell spellchecker

### Handler
- [ ] MonologHandler
- [ ] ChainedHandler
- [ ] HTMLReportHandler
- [ ] XmlReportHandler
- [ ] JSONReportHandler
- [ ] ConsoleTableHandler

### Tests
- [ ] Add or improve tests with different text encoding
- [ ] Refactor duplicated Dockerfile content between php images


## Versioning
We try to follow [SemVer v2.0.0](http://semver.org/)

There's still a lot of design decisions that should be confronted to real world
usage before thinking about a v1.0.0 stable release:
- Are `TextInterface` and `MisspellingInterface` really useful ?
- Does using generators is the right way to go ?
- Should all the contributed spellcheckers be maintained by the package itself ?
- How to design an intuitive cli given the flexibility of usage needed ?
- Is the "context" array passed through all the layer the right design to handle data sharing ?

## Testing

Spellcheckers comes in a lot of different form, from HTTP API to command line tools.
As **PHP-Spellcheck** wants to ensure real world usage is OK, it contains integration tests.

Therefore to run these integration tests, these spellcheckers must all be available during tests execution.

The more convenient way to do it is using **docker** and avoid polluting your own local machine with installed spellcheckers systems only required for this package tests.

### Docker
Requires `docker` and `docker-compose` to be installed. Tested on `Linux`.
``` bash
$ make build # build containers images
$ make setup # start spellcheckers container
$ make tests-dox
```

You can also specify php version, dependency version target and if you want coverage. Coverage is only supported by php7.2 for now.
```
$ PHP_VERSION=7.2 DEPS=LOWEST WITH_COVERAGE="true" make tests-dox
```

Run `make help` to list all tasks available

### Local

Todo

### Environment variables
If spellcheckers execution path are different than their default value
(e.g. `docker exec -ti myispell` instead of `ispell`) you can override the path used in tests
by redefining env vars in [PHPUnit config file](phpunit.xml.dist)

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md)

## Credits

- Inspired by [php-speller](https://github.com/mekras/php-speller) and [monolog](https://github.com/Seldaek/monolog)
- [Philippe Segatori][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

**Logo**:
Elements taken for the final rendering are [Designed by rawpixel.com / Freepik](http://www.freepik.com)

[link-author]: https://github.com/tigitz
[link-contributors]: ../../contributors
[aspell]: https://github.com/GNUAspell/aspell
[hunspell]: https://github.com/hunspell/hunspell
[ispell]: https://packages.debian.org/stretch/ispell
[languagetools]: https://github.com/languagetool-org/languagetool
[pspell]: http://php.net/manual/fr/book.pspell.php

[pspell]: http://php.net/manual/fr/book.pspell.php
