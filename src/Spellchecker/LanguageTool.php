<?php

declare(strict_types=1);

namespace PhpSpellcheck\Spellchecker;

use PhpSpellcheck\Exception\RuntimeException;
use PhpSpellcheck\Misspelling;
use PhpSpellcheck\Spellchecker\LanguageTool\LanguageToolApiClient;
use PhpSpellcheck\Utils\LineAndOffset;
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
     * @param array<mixed> $context
     * @param array<string> $languages
     *
     * @return \Generator<Misspelling>
     */
    public function check(
        string $text,
        array $languages,
        array $context
    ): iterable {
        Assert::notEmpty($languages, 'LanguageTool requires at least one language to be set to run it\'s spellchecking process');

        if (isset($context[self::class])) {
            Assert::isArray($context[self::class]);
            /** @var array<mixed> $options */
            $options = $context[self::class];
        }
        $check = $this->apiClient->spellCheck($text, $languages, $options ?? []);

        if (!\is_array($check['matches'])) {
            throw new RuntimeException('LanguageTool spellcheck response must contain a "matches" array');
        }

        foreach ($check['matches'] as $match) {
            [$line, $offsetFromLine] = LineAndOffset::findFromFirstCharacterOffset(
                $text,
                $match['offset']
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
     * @return array<string>
     */
    public function getSupportedLanguages(): array
    {
        return $this->apiClient->getSupportedLanguages();
    }
}
