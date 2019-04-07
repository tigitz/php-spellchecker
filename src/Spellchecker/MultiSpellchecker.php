<?php

declare(strict_types=1);

namespace PhpSpellcheck\Spellchecker;

use PhpSpellcheck\MisspellingInterface;
use Webmozart\Assert\Assert;

class MultiSpellchecker implements SpellcheckerInterface
{
    /**
     * @var iterable<SpellcheckerInterface>
     */
    private $spellCheckers;

    /**
     * @var bool
     */
    private $mergeMisspellingsSuggestions;

    /**
     * @param SpellcheckerInterface[] $spellCheckers
     */
    public function __construct(iterable $spellCheckers, bool $mergeMisspellingsSuggestions = true)
    {
        Assert::allIsInstanceOf($spellCheckers, SpellcheckerInterface::class);
        $this->spellCheckers = $spellCheckers;
        $this->mergeMisspellingsSuggestions = $mergeMisspellingsSuggestions;
    }

    /**
     * {@inheritdoc}
     */
    public function check(string $text, array $languages = [], array $context = [], ?string $encoding = null): iterable
    {
        if (!$this->mergeMisspellingsSuggestions) {
            return $this->checkForAllSpellcheckers($text, $languages, $context, $encoding);
        }

        /** @var MisspellingInterface[] $misspellings */
        $misspellings = [];
        /** @var SpellcheckerInterface $spellChecker */
        foreach ($this->spellCheckers as $spellChecker) {
            $supportedLanguages = is_array($spellChecker->getSupportedLanguages()) ?
                $spellChecker->getSupportedLanguages() :
                iterator_to_array($spellChecker->getSupportedLanguages());

            $spellcheckerSupportedLanguages = array_intersect($supportedLanguages, $languages);

            if ($spellcheckerSupportedLanguages === []) {
                continue;
            }

            foreach ($spellChecker->check($text, $spellcheckerSupportedLanguages, $context, $encoding) as $misspelling) {
                if (!empty($context)) {
                    $misspelling = $misspelling->mergeContext($context);
                }

                if (!$misspelling->canDeterminateUniqueIdentity()) {
                    $misspellings[] = $misspelling;
                    continue;
                }

                if (isset($misspellings[$misspelling->getUniqueIdentity()])) {
                    $misspellings[$misspelling->getUniqueIdentity()]->mergeSuggestions($misspelling->getSuggestions());
                    continue;
                }

                $misspellings[$misspelling->getUniqueIdentity()] = $misspelling;
            }
        }

        return array_values($misspellings);
    }

    /**
     * {@inheritdoc}
     */
    public function getSupportedLanguages(): iterable
    {
        $supportedLanguages = [];
        foreach ($this->spellCheckers as $spellChecker) {
            foreach ($spellChecker->getSupportedLanguages() as $language) {
                $supportedLanguages[] = $language;
            }
        }

        return array_values(array_unique($supportedLanguages));
    }

    private function checkForAllSpellcheckers(
        string $text,
        array $languages,
        array $context,
        ?string $encoding
    ): iterable {
        foreach ($this->spellCheckers as $spellChecker) {
            $supportedLanguages = is_array($spellChecker->getSupportedLanguages()) ?
                $spellChecker->getSupportedLanguages() :
                iterator_to_array($spellChecker->getSupportedLanguages());

            $spellcheckerSupportedLanguages = array_intersect($supportedLanguages, $languages);

            if ($spellcheckerSupportedLanguages === []) {
                continue;
            }

            foreach ($spellChecker->check($text, $spellcheckerSupportedLanguages, $context, $encoding) as $misspelling) {
                if (!empty($context)) {
                    $misspelling = $misspelling->mergeContext($context);
                }

                yield $misspelling;
            }
        }
    }
}
