<h1 align="center">PHP-Spellcheck</h1>

<p align="center">
    <img src="https://i.imgur.com/C8hHwW9.png" alt="PHP-Spellcheck" width="300" height="300">
</p>
<p align="center">
    <a href="https://travis-ci.org/tigitz/php-spellcheck"><img src="https://img.shields.io/travis/com/tigitz/php-spellcheck/master.svg?style=flat-square&logo=travis" alt="Build Status"></a>
    <a href="https://codecov.io/gh/tigitz/php-spellcheck/branch/master"><img src="https://img.shields.io/codecov/c/github/tigitz/php-spellcheck/master.svg?style=flat-square&logo=codecov" alt="Code coverage"></a>
    <a href="https://scrutinizer-ci.com/g/tigitz/php-spellcheck/?branch=master"><img src="https://img.shields.io/scrutinizer/g/tigitz/php-spellcheck.svg?style=flat-square&logo=scrutinizer" alt="Code coverage"></a>
    <a href="https://gitter.im/php-spellcheck/community"><img src="https://img.shields.io/gitter/room/tigitz/php-spellcheck.svg?style=flat-square" alt="PHP-Spellcheck chat room"></a>
    <a href="https://choosealicense.com/licenses/mit/"><img src="https://img.shields.io/github/license/tigitz/php-spellcheck.svg?style=flat-square" alt="License"></a>
</p>

<p align="center">Check misspellings from any text source with the most popular PHP spellchecker.</p>


------
# About

PHP-Spellcheck is a spellchecker abstraction library for PHP. By providing a unified interface for many different spellcheckers, you’re able to swap out spellcheckers without extensive rewrites.

Using PHP-Spellcheck can eliminate vendor-lock in, reduce technical debt, and improve the testability of your code.

# Features

- 🧐 Supports many popular spellcheckers out of the box: [Aspell][aspell], [Hunspell][hunspell], [Ispell][ispell], [PHP Pspell][pspell], [LanguageTools][languagetools] and [MultiSpellchecker](src/Spellchecker/MultiSpellchecker.php) [(add yours!)](docs/spellchecker/create-custom.md)
- 📄 Supports different text sources: filesystem [file](src/Source/File.php)/[directory](src/Source/Directory.php), [string](src/Source/PHPString.php), and [multisource](src/Source/MultipleSource.php) [(add yours!)](docs/spellchecker/create-custom.md)
- 🛠 Supports text processors: [MarkdownRemover](src/TextProcessor/MarkdownRemover.php) [(add yours!)](docs/text-proccesor/create-custom.md)
- 🔁 Supports misspelling handlers: [EchoHandler](src/MisspellingHandler/EchoHandler.php) [(add yours!)](docs/misspellings-handler/create-custom.md)
- ➰ Makes use of generators to reduce memory footprint
- ⚖ Flexible and straightforward design
- 💡 Makes it a breeze to implement your own spellcheckers, text processors and misspellings handlers
- 💪 Runs tests against real spellcheckers to ensure full compatibility

**PHP-Spellcheck** is a welcoming project for new contributors.

Want to make **your first open source contribution**? Check the [roadmap](#roadmap), pick one task, [open an issue](https://github.com/tigitz/php-spellcheck/issues/new) and we'll help you go through it 🤓🚀

# Install

Via Composer

```sh
$ composer require tigitz/php-spellcheck
```

# Usage

## Using the spellchecker directly

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

# Roadmap

## Global

- [ ] Add a CLI that could do something like `vendor/bin/php-spellcheck "misspell" Languagetools EchoHandler --lang=en_US`
- [ ] Add asynchronous mechanism to spellcheckers
- [ ] Make some computed misspelling properties optional to improve performance for certain use cases (e.g., lines and offset in `LanguageTools`)
- [ ] Add a languages mapper to manage their different representations across spellcheckers
- [ ] Evaluate `strtok` instead of `explode` to parse lines of text, for performance
- [ ] Evaluate `MutableMisspelling` for performance comparison
- [ ] Wrap `Webmozart/Assert` library exceptions to throw PHP-Spellcheck custom exceptions instead
- [ ] Improve the `Makefile`

## Sources

- [ ] Make a `SourceInterface` class that's able to have an effect on the used spellchecker configuration
- [ ] League/Flysystem plugin
- [ ] Symfony/Finder plugin

## Text processors

- [ ] Markdown - Find a way to keep original offset and line of words after stripping
- [ ] Add PHPDoc processor

## Spellcheckers

- [ ] Cache suggestions of already spellchecked words (PSR-6/PSR-16?)
- [ ] Pspell - Find way to compute word offset
- [ ] LanguageTools - Evaluate [HTTPlug library][httplug] to make API requests
- [ ] Pspell - find way to list available dictionaries
- [ ] Add JamSpell spellchecker
- [ ] Add NuSpell spellchecker

## Handlers

- [ ] MonologHandler
- [ ] ChainedHandler
- [ ] HTMLReportHandler
- [ ] XmlReportHandler
- [ ] JSONReportHandler
- [ ] ConsoleTableHandler

## Tests

- [ ] Add or improve tests with different text encoding
- [ ] Refactor duplicate Dockerfile content between PHP images


# Versioning

We follow [SemVer v2.0.0](http://semver.org/).

There still are many design decisions that should be confronted with real-world usage before thinking about a v1.0.0 stable release:

- Are `TextInterface` and `MisspellingInterface` really useful?
- Is using generators the right way to go?
- Should all the contributed spellcheckers be maintained by the package itself?
- How to design an intuitive CLI given the needed flexibility of usage?
- Is the "context" array passed through all the layers the right design to handle data sharing?

# Testing

Spellcheckers come in many different forms, from HTTP API to command line tools. **PHP-Spellcheck** wants to ensure real-world usage is OK, so it contains integration tests. To run these, spellcheckers need to all be available during tests execution.

The most convenient way to do it is by using Docker and avoid polluting your local machine.

## Docker

Requires `docker` and `docker-compose` to be installed (tested on Linux).

```sh
$ make build # build container images
$ make setup # start spellcheckers container
$ make tests-dox
```

You can also specify PHP version, dependency version target and if you want coverage. Coverage is only supported by PHP 7.2 for now.

```sh
$ PHP_VERSION=7.2 DEPS=LOWEST WITH_COVERAGE="true" make tests-dox
```

Run `make help` to list all available tasks.

## Locally

Todo

## Environment variables

If spellcheckers execution paths are different than their default values
(e.g., `docker exec -ti myispell` instead of `ispell`) you can override the path used in tests by redefining environment variables in the [PHPUnit config file](phpunit.xml.dist)

# Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md).

# Credits

- Inspired by [php-speller](https://github.com/mekras/php-speller), [monolog](https://github.com/Seldaek/monolog) and [flysystem](https://github.com/thephpleague/flysystem)
- [Philippe Segatori][link-author]
- [All Contributors][link-contributors]

# License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

**Logo**:
Elements taken for the final rendering are [Designed by rawpixel.com / Freepik](http://www.freepik.com)

[link-author]: https://github.com/tigitz
[link-contributors]: ../../contributors
[aspell]: https://github.com/GNUAspell/aspell
[hunspell]: https://github.com/hunspell/hunspell
[ispell]: https://packages.debian.org/stretch/ispell
[languagetools]: https://github.com/languagetool-org/languagetool
[pspell]: http://php.net/manual/en/book.pspell.php
[httplug]: https://github.com/php-http/httplug
