<?php

declare(strict_types=1);

namespace PhpSpellcheck\Spellchecker;

use PhpSpellcheck\Misspelling;
use PhpSpellcheck\Spellchecker\LanguageTool\LanguageToolApiClient;
use PhpSpellcheck\Utils\SortedNumericArrayNearestValueFinder;
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
        ?string $encoding = TextEncoding::UTF8
    ): iterable {
        Assert::notEmpty($languages, 'LanguageTool requires at least one language to run it\'s spellchecking process');

        $check = $this->apiClient->spellCheck($text, $languages, $context[self::class] ?? []);
        $lineBreaksOffset = $this->getLineBreaksOffset($text, $encoding);

        foreach ($check['matches'] as $match) {
            list($offsetFromLine, $line) = $this->computeRealOffsetAndLine($match, $lineBreaksOffset);

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

    private function computeRealOffsetAndLine(array $match, array $lineBreaksOffset): array
    {
        $languageToolsOffset = (int) $match['offset'];
        $index = SortedNumericArrayNearestValueFinder::findIndex(
            (int) $match['offset'],
            $lineBreaksOffset,
            SortedNumericArrayNearestValueFinder::FIND_HIGHER
        );

        if ($index === 0) {
            // word is on the first line
            $offsetFromLine = $languageToolsOffset;
            $line = $index + 1;
        } else {
            if ($languageToolsOffset > $lineBreaksOffset[$index]) {
                // word is on the last line
                $offsetFromLine = $languageToolsOffset - $lineBreaksOffset[$index];
                $line = $index + 2;
            } else {
                $offsetFromLine = $languageToolsOffset - $lineBreaksOffset[$index - 1];
                $line = $index + 1;
            }
        }

        return [$offsetFromLine, $line];
    }

    private function getLineBreaksOffset(string $text, ?string $encoding): array
    {
        if ($encoding === null) {
            $encoding = \Safe\mb_internal_encoding();
        }

        $start = 0;
        // First line has a line offset at 0
        $lineBreaksOffset = [$start];
        while (($pos = \mb_strpos(($text), PHP_EOL, $start, $encoding)) != false) {
            $lineBreaksOffset[] = $pos;
            $start = $pos + 1; // start searching from next position.
        }

        return $lineBreaksOffset;
    }
}
