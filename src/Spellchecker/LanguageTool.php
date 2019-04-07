<?php

declare(strict_types=1);

namespace PhpSpellcheck\Spellchecker;

use PhpSpellcheck\Misspelling;
use PhpSpellcheck\Spellchecker\LanguageTool\LanguageToolApiClient;
use PhpSpellcheck\Utils\LineAndOffset;
use PhpSpellcheck\Utils\TextEncoding;
use Webmozart\Assert\Assert;

class LanguageTool implements SpellcheckerInterface
{
    /**
     * @var LanguageToolApiClient
     */
    private $apiClient;

    public function __construct(LanguageToolApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    /**
     * @return Misspelling[]
     */
    public function check(
        string $text,
        array $languages = [],
        array $context = [],
        ?string $encoding
    ): iterable {
        Assert::notEmpty($languages, 'LanguageTool requires at least one language to run it\'s spellchecking process');

        $check = $this->apiClient->spellCheck($text, $languages, $context[self::class] ?? []);

        foreach ($check['matches'] as $match) {
            list($line, $offsetFromLine) = LineAndOffset::findFromFirstCharacterOffset(
                $text,
                $match['offset'],
                $encoding ?? TextEncoding::detect($text)
            );

            yield new Misspelling(
                mb_substr($match['context']['text'], $match['context']['offset'], $match['context']['length']),
                $offsetFromLine,
                $line, // line break index transformed in line number
                array_column($match['replacements'], 'value'),
                array_merge(
                    [
                        'sentence' => $match['sentence'],
                        'spellingErrorMessage' => $match['message'],
                        'ruleUsed' => $match['rule'],
                    ],
                    $context
                )
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getSupportedLanguages(): iterable
    {
        return $this->apiClient->getSupportedLanguages();
    }
}
