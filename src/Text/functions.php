<?php

declare(strict_types=1);

namespace PhpSpellcheck;

if (!\function_exists(t::class)) {
    /**
     * @param array<mixed> $context
     */
    function t(string $string = '', array $context = []): Text
    {
        return new Text($string, $context);
    }
}
