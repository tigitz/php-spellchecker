<?php

declare(strict_types=1);

namespace PhpSpellcheck\Spellchecker;

use PhpSpellcheck\Misspelling;

interface SpellcheckerInterface
{
    /**
     * @return Misspelling[]
     */
    public function check(
        string $text,
        array $languages,
        array $context,
        ?string $encoding
    ): iterable;

    /**
     * @return string[]
     */
    public function getSupportedLanguages(): iterable;
}
