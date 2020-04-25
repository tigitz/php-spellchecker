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
     * @return array<string>
     */
    public function getSuggestions(): array;

    /**
     * @param array<string> $suggestions
     */
    public function mergeSuggestions(array $suggestions): MisspellingInterface;

    /**
     * @return array<mixed>
     */
    public function getContext(): array;

    /**
     * @param array<mixed> $context
     */
    public function setContext(array $context): MisspellingInterface;

    public function hasContext(): bool;

    /**
     * @param array<mixed> $context
     */
    public function mergeContext(array $context, bool $override = true): MisspellingInterface;

    public function getUniqueIdentity(): string;

    public function canDeterminateUniqueIdentity(): bool;
}
