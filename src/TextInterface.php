<?php

declare(strict_types=1);

namespace PhpSpellcheck;

interface TextInterface
{
    public function getContent(): string;

    public function getEncoding(): string;

    public function getContext(): array;

    public function replaceContent(string $newContent): TextInterface;

    public function mergeContext(array $context, bool $override): TextInterface;
}
