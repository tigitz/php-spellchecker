<?php

declare(strict_types=1);

namespace PhpSpellcheck\Spellchecker;

interface SpellcheckerInterface
{
    public function check(
        string $text,
        array $languages,
        array $context
    ): iterable;

    /**
     * @return string[]
     */
    public function getSupportedLanguages(): iterable;
}
