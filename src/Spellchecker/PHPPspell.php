<?php

declare(strict_types=1);

namespace PhpSpellcheck\Spellchecker;

use PhpSpellcheck\Exception\LogicException;
use PhpSpellcheck\Exception\RuntimeException;
use PhpSpellcheck\Misspelling;
use PhpSpellcheck\Utils\TextEncoding;
use Webmozart\Assert\Assert;

class PHPPspell implements SpellcheckerInterface
{
    /**
     * @var int
     */
    private $mode;

    /**
     * @var int
     */
    private $numberOfCharactersLowerLimit;

    /**
     * @see http://php.net/manual/en/function.pspell-config-mode.php
     * @see http://php.net/manual/en/function.pspell-config-ignore.php
     *
     * @param int $mode the mode parameter is the mode in which the spellchecker will work
     * @param int $numberOfCharactersLowerLimit Words less than n characters will be skipped
     */
    public function __construct(?int $mode = null, int $numberOfCharactersLowerLimit = 0)
    {
        if (!extension_loaded('pspell')) {
            throw new RuntimeException('Pspell extension must be loaded to use the PHPPspell spellchecker');
        }

        if ($mode === null) {
            $mode = PSPELL_FAST;
        }

        Assert::greaterThanEq($numberOfCharactersLowerLimit, 0);

        $this->mode = $mode;
        $this->numberOfCharactersLowerLimit = $numberOfCharactersLowerLimit;
    }

    /**
     * {@inheritdoc}
     */
    public function check(
        string $text,
        array $languages = [],
        array $context = [],
        ?string $encoding = TextEncoding::UTF8
    ): iterable {
        Assert::count($languages, 1, 'PHPPspell spellchecker doesn\'t support multiple languages check');
        Assert::notNull($encoding, 'PHPPspell requires the encoding to be defined');

        $pspellConfig = \Safe\pspell_config_create(current($languages), '', '', $encoding);
        \Safe\pspell_config_mode($pspellConfig, $this->mode);
        \Safe\pspell_config_ignore($pspellConfig, $this->numberOfCharactersLowerLimit);
        $dictionary = \Safe\pspell_new_config($pspellConfig);

        $lines = explode(PHP_EOL, $text);

        /** @var string $line */
        foreach ($lines as $lineNumber => $line) {
            $words = explode(' ', \Safe\preg_replace("/(?!['’-])(\p{P}|\+|--)/u", '', $line));
            foreach ($words as $key => $word) {
                if (!pspell_check($dictionary, $word)) {
                    $suggestions = pspell_suggest($dictionary, $word);
                    yield new Misspelling($word, 0, $lineNumber + 1, $suggestions, $context);
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getSupportedLanguages(): iterable
    {
        throw new LogicException('Retrieving supported dictionaries for PHPPspell spellchecker is not supported yet');
    }
}
