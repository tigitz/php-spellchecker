<?php

declare(strict_types=1);

namespace PhpSpellcheck\Source;

use PhpSpellcheck\TextInterface;

interface SourceInterface
{
    /**
     * @param array<mixed> $context
     *
     * @return iterable<TextInterface>
     */
    public function toTexts(array $context): iterable;
}
