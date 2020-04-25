<?php

declare(strict_types=1);

use PhpSpellcheck\Text;

/**
 * @param array<mixed> $context
 */
function t(string $string = '', array $context = []): Text
{
    return new Text($string, $context);
}
