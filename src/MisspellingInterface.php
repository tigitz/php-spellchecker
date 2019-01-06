<?php

declare(strict_types=1);

namespace PhpSpellcheck;

interface MisspellingInterface
{
    public function getWord(): string;

    public function getOffset(): ?int;

    public function getLineNumber(): ?int;

    public function hasSuggestions(): bool;

    /**
     * @return string[]
     */
    public function getSuggestions(): array;

    public function mergeSuggestions(array $suggestions): MisspellingInterface;

    public function getContext(): array;

    public function setContext(array $context): MisspellingInterface;

    public function hasContext(): bool;

    public function mergeContext(array $context, bool $override = true): MisspellingInterface;

    public function getUniqueIdentity(): string;

    public function canDeterminateUniqueIdentity(): bool;
}
