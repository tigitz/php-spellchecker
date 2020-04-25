<?php

declare(strict_types=1);

namespace PhpSpellcheck\Spellchecker;

use PhpSpellcheck\MisspellingInterface;

interface SpellcheckerInterface
{
    /**
     * @param array<mixed> $context
     * @param array<string> $languages
     *
     * @return iterable<MisspellingInterface>
     */
    public function check(
        string $text,
        array $languages,
        array $context
    ): iterable;

    /**
     * @return iterable<string>
     */
    public function getSupportedLanguages(): iterable;
}
