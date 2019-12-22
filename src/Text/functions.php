<?php

declare(strict_types=1);

use PhpSpellcheck\Text;

function t(string $string = '', array $context = []): Text
{
    return Text::create($string, $context);
}
