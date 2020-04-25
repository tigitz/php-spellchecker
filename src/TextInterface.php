<?php

declare(strict_types=1);

namespace PhpSpellcheck;

interface TextInterface
{
    public function getContent(): string;

    /**
     * @return array<mixed>
     */
    public function getContext(): array;

    public function replaceContent(string $newContent): TextInterface;

    /**
     * @param array<mixed> $context
     */
    public function mergeContext(array $context, bool $override): TextInterface;
}
