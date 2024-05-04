<h1 align="center">PHP-Spellchecker</h1>

<p align="center">
    <img src="https://i.imgur.com/C8hHwW9.png" alt="PHP-Spellchecker" width="300" height="300">
</p>
<p align="center">
    <a href="https://github.com/tigitz/php-spellchecker/actions"><img src="https://img.shields.io/github/checks-status/tigitz/php-spellchecker/master?logo=github&style=flat-square" alt="Build Status"></a>
    <a href="https://codecov.io/gh/tigitz/php-spellchecker/branch/master"><img src="https://img.shields.io/codecov/c/github/tigitz/php-spellchecker/master.svg?style=flat-square&logo=codecov" alt="Code coverage"></a>
    <a href="https://scrutinizer-ci.com/g/tigitz/php-spellchecker/?branch=master"><img src="https://img.shields.io/scrutinizer/g/tigitz/php-spellchecker.svg?style=flat-square&logo=scrutinizer" alt="Code coverage"></a>
    <a href="https://gitter.im/php-spellchecker/php-spellchecker"><img src="https://img.shields.io/gitter/room/tigitz/php-spellchecker.svg?style=flat-square" alt="PHP-Spellchecker chat room"></a>
    <a href="https://choosealicense.com/licenses/mit/"><img src="https://img.shields.io/github/license/tigitz/php-spellchecker.svg?style=flat-square" alt="License"></a>
</p>

<p align="center">Check misspellings from any text source with the most popular PHP spellchecker.</p>


------
# About

PHP-Spellchecker is a spellchecker abstraction library for PHP. By providing a unified interface for many different spellcheckers, you’re able to swap out spellcheckers without extensive rewrites.

Using PHP-Spellchecker can eliminate vendor lock-in, reduce technical debt, and improve the testability of your code.

# Features

- 🧐 Supports many popular spellcheckers out of the box: [Aspell][aspell], [Hunspell][hunspell], [Ispell][ispell], [PHP Pspell][pspell], [LanguageTools][languagetools], [JamSpell][jamspell] and [MultiSpellchecker][multispellchecker] [(add yours!)][spellchecker_custom]
- 📄 Supports different text sources: file system [file][filesource]/[directory][directory], [string][php-string], and [multi-source][multisource] [(add yours!)][source_custom]
- 🛠 Supports text processors: [MarkdownRemover][markdownremover] [(add yours!)][textprocessor_custom]
- 🔁 Supports misspelling handlers: [EchoHandler][echohandler] [(add yours!)][custom_handler]
- ➰ Makes use of generators to reduce memory footprint
- ⚖ Flexible and straightforward design
- 💡 Makes it a breeze to implement your own spellcheckers, text processors and misspellings handlers
- 💪 Runs tests against real spellcheckers to ensure full compatibility

**PHP-Spellchecker** is a welcoming project for new contributors.

Want to make **your first open source contribution**? Check the [roadmap](#roadmap), pick one task, [open an issue](https://github.com/tigitz/php-spellchecker/issues/new) and we'll help you go through it 🤓🚀

# Install

Via Composer

```sh
$ composer require tigitz/php-spellchecker
```

# Usage

[Check out the documentation](https://tigitz.github.io/php-spellchecker) and [examples](https://github.com/tigitz/php-spellchecker/tree/master/examples)

## Using the spellchecker directly

You can check misspellings directly from a `PhpSpellcheck\Spellchecker` class and process them on your own.

```php
<?php
// if you made the default aspell installation on your local machine
$aspell = Aspell::create();

// or if you want to use binaries from Docker
$aspell = new Aspell(new CommandLine(['docker','run','--rm', '-i', 'starefossen/aspell']));

$misspellings = $aspell->check('mispell', ['en_US'], ['from_example']);
foreach ($misspellings as $misspelling) {
    $misspelling->getWord(); // 'mispell'
    $misspelling->getLineNumber(); // '1'
    $misspelling->getOffset(); // '0'
    $misspelling->getSuggestions(); // ['misspell', ...]
    $misspelling->getContext(); // ['from_example']
}
```

## Using the `MisspellingFinder` orchestrator

You can also use an opinionated `MisspellingFinder` class to orchestrate your spellchecking flow:

<p align="center">
    <img src="https://i.imgur.com/n3JjWgh.png" alt="PHP-Spellchecker-misspellingfinder-flow">
</p>

Following the well-known [Unix philosophy](http://en.wikipedia.org/wiki/Unix_philosophy):
> Write programs that do one thing and do it well. Write programs to work together. Write programs to handle text streams, because that is a universal interface.

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

// using a string
$misspellingFinder->find('It\'s_a_mispelling', ['en_US']);
// word: mispelling | line: 1 | offset: 7 | suggestions: mi spelling,mi-spelling,misspelling | context: []

// using a TextSource
$inMemoryTextProvider = new class implements SourceInterface
{
    public function toTexts(array $context): iterable
    {
        yield new Text('my_mispell', ['from_source_interface']);
        // t() is a shortcut for new Text()
        yield t('my_other_mispell', ['from_named_constructor']);
    }
};

$misspellingFinder->find($inMemoryTextProvider, ['en_US']);
//word: mispell | line: 1 | offset: 3 | suggestions: mi spell,mi-spell,misspell,... | context: ["from_source_interface"]
//word: mispell | line: 1 | offset: 9 | suggestions: mi spell,mi-spell,misspell,... | context: ["from_named_constructor"]
```

# Roadmap

The project is still in its initial phase, requiring more real-life usage to stabilize its final 1.0.0 API.

## Global

- [ ] Add a CLI that could do something like `vendor/bin/php-spellchecker "misspell" Languagetools EchoHandler --lang=en_US`
- [ ] Add asynchronous mechanism to spellcheckers.
- [ ] Make some computed misspelling properties optional to improve performance for certain use cases (e.g., lines and offset in `LanguageTools`).
- [ ] Add a language mapper to manage different representations across spellcheckers.
- [ ] Evaluate `strtok` instead of `explode` to parse lines of text, for performance.
- [ ] Evaluate `MutableMisspelling` for performance comparison.
- [ ] Wrap `Webmozart/Assert` library exceptions to throw PHP-Spellchecker custom exceptions instead.
- [ ] Improve the `Makefile`.

## Sources

- [ ] Make a `SourceInterface` class that's able to have an effect on the used spellchecker configuration.
- [ ] `League/Flysystem` source.
- [ ] `Symfony/Finder` source.

## Text processors

- [ ] Markdown - Find a way to keep the original offset and line of words after stripping.
- [ ] Add PHPDoc processor.
- [ ] Add HTML Processor ([inspiration](https://github.com/mekras/php-speller/blob/master/src/Source/Filter/HtmlFilter.php)).
- [ ] Add XLIFF Processor ([inspiration](https://github.com/mekras/php-speller/blob/master/src/Source/XliffSource.php)).

## Spell checkers

- [ ] Cache suggestions of already spellchecked words (PSR-6/PSR-16?).
- [ ] Pspell - Find way to compute word offset.
- [ ] LanguageTools - Evaluate [HTTPlug library][httplug] to make API requests.
- [x] Pspell - find way to list available dictionaries.
- [x] Add [JamSpell](https://github.com/bakwc/JamSpell#http-api) spellchecker.
- [ ] Add [NuSpell](https://github.com/nuspell/nuspell) spellchecker.
- [ ] Add [SymSpell](https://github.com/LeonErath/SymSpellAPI) spellchecker.
- [ ] Add [Yandex.Speller API](https://yandex.ru/dev/speller/doc/dg/concepts/api-overview-docpage/) spellchecker.
- [ ] Add [Bing Spell Check API](https://docs.microsoft.com/en-us/azure/cognitive-services/bing-spell-check/overview) spellchecker.

## Handlers

- [ ] MonologHandler
- [ ] ChainedHandler
- [ ] HTMLReportHandler
- [ ] XmlReportHandler
- [ ] JSONReportHandler
- [ ] ConsoleTableHandler

## Tests

- [ ] Add or improve tests with different text encoding.
- [ ] Refactor duplicate Dockerfile content between PHP images.


# Versioning

We follow [SemVer v2.0.0](http://semver.org/).

There still are many design decisions that should be confronted with real-world usage before thinking about a v1.0.0 stable release:

- Are `TextInterface` and `MisspellingInterface` really useful?
- Is using generators the right way to go?
- Should all the contributed spellcheckers be maintained by the package itself?
- How to design an intuitive CLI given the needed flexibility of usage?
- Is the "context" array passed through all the layers the right design to handle data sharing?

# Testing

Spell checkers come in many different forms, from HTTP API to command line tools. **PHP-Spellchecker** wants to ensure real-world usage is OK, so it contains integration tests. To run these, spellcheckers need to all be available during tests execution.

The most convenient way to do it is by using Docker and avoid polluting your local machine.

## Docker

Requires `docker` and `docker-compose` to be installed (tested on Linux).

```sh
$ make build # build container images
$ make setup # start spellcheckers container
$ make tests-dox
```

You can also specify PHP version, dependency version target and if you want coverage.

```sh
$ PHP_VERSION=8.2 DEPS=LOWEST WITH_COVERAGE="true" make tests-dox
```

Run `make help` to list all available tasks.

## Environment variables

If spellcheckers execution paths are different than their default values (e.g., `docker exec -ti myispell` instead of `ispell`) you can override the path used in tests by redefining environment variables in the [PHPUnit config file](https://github.com/tigitz/php-spellchecker/blob/master/phpunit.xml.dist).

# Contributing

Please see [CONTRIBUTING](https://github.com/tigitz/php-spellchecker/tree/master/examples).

# Credits

- Inspired by [php-speller](https://github.com/mekras/php-speller), [monolog](https://github.com/Seldaek/monolog) and [flysystem](https://github.com/thephpleague/flysystem)
- [Philippe Segatori][link-author]
- [All Contributors][link-contributors]

# License

The MIT License (MIT). Please see [license file](https://github.com/tigitz/php-spellchecker/blob/master/LICENSE.md) for more information.

**Logo**:
Elements taken for the final rendering are [Designed by rawpixel.com / Freepik](http://www.freepik.com).

[link-author]: https://github.com/tigitz
[link-contributors]: ../../contributors

[aspell]: https://tigitz.github.io/php-spellchecker/docs/spellcheckers/aspell.html
[hunspell]: https://tigitz.github.io/php-spellchecker/docs/spellcheckers/hunspell.html
[ispell]: https://tigitz.github.io/php-spellchecker/docs/spellcheckers/ispell.html
[languagetools]: https://tigitz.github.io/php-spellchecker/docs/spellcheckers/languagetools.html
[jamspell]: https://tigitz.github.io/php-spellchecker/docs/spellcheckers/jamspell.html
[pspell]: https://tigitz.github.io/php-spellchecker/docs/spellcheckers/php-pspell.html
[multispellchecker]: https://tigitz.github.io/php-spellchecker/docs/spellcheckers/multispellchecker.html
[spellchecker_custom]: https://tigitz.github.io/php-spellchecker/docs/spellcheckers/create-custom.html

[echohandler]: https://tigitz.github.io/php-spellchecker/docs/misspellings-handlers/echohandler.html
[custom_handler]: https://tigitz.github.io/php-spellchecker/docs/misspellings-handlers/create-custom.html

[filesource]: https://tigitz.github.io/php-spellchecker/docs/text-sources/file.html
[directory]: https://tigitz.github.io/php-spellchecker/docs/text-sources/directory.html
[php-string]: https://tigitz.github.io/php-spellchecker/docs/text-sources/php-string.html
[multisource]: https://tigitz.github.io/php-spellchecker/docs/text-sources/multisource.html
[source_custom]: https://tigitz.github.io/php-spellchecker/docs/text-sources/create-custom.html

[markdownremover]: https://tigitz.github.io/php-spellchecker/docs/text-processors/markdown-remover.html
[textprocessor_custom]: https://tigitz.github.io/php-spellchecker/docs/text-processors/create-custom.html

[httplug]: https://github.com/php-http/httplug
