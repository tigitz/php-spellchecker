<?php

declare(strict_types=1);

namespace PhpSpellcheck\Source;

use PhpSpellcheck\TextInterface;

interface SourceInterface
{
    /**
     * @return TextInterface[]
     */
    public function toTexts(array $context): iterable;
}
