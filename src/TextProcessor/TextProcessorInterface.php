<?php

declare(strict_types=1);

namespace PhpSpellcheck\TextProcessor;

use PhpSpellcheck\TextInterface;

interface TextProcessorInterface
{
    public function process(TextInterface $text): TextInterface;
}
