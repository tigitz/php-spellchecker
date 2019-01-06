<?php

declare(strict_types=1);

namespace PhpSpellcheck;

use PhpSpellcheck\Exception\InvalidArgumentException;
use Webmozart\Assert\Assert;

class Misspelling implements MisspellingInterface
{
    /**
     * @var string
     */
    private $word;

    /**
     * @var null|int
     */
    private $offset;

    /**
     * @var null|int
     */
    private $lineNumber;

    /**
     * @var string[]
     */
    private $suggestions;

    /**
     * @var array
     */
    private $context;

    public function __construct(
        string $word,
        ?int $offset = null,
        ?int $lineNumber = null,
        array $suggestions = [],
        array $context = []
    ) {
        Assert::stringNotEmpty($word);

        $this->word = $word;
        $this->offset = $offset;
        $this->lineNumber = $lineNumber;
        $this->suggestions = $suggestions;
        $this->context = $context;
    }

    public function mergeSuggestions(array $suggestionsToAdd): MisspellingInterface
    {
        $mergedSuggestions = [];
        $existingSuggestionsAsKeys = array_flip($this->suggestions);
        foreach ($suggestionsToAdd as $suggestionToAdd) {
            if (!isset($existingSuggestionsAsKeys[$suggestionToAdd])) {
                $this->suggestions[] = $suggestionToAdd;
            }
        }

        return new self(
            $this->word,
            $this->offset,
            $this->lineNumber,
            $mergedSuggestions,
            $this->context
        );
    }

    public function getUniqueIdentity(): string
    {
        return $this->getWord() . $this->getLineNumber() . $this->getOffset();
    }

    public function canDeterminateUniqueIdentity(): bool
    {
        return $this->getLineNumber() !== null
            && $this->getOffset() !== null;
    }

    public function getWord(): string
    {
        return $this->word;
    }

    public function getOffset(): ?int
    {
        return $this->offset;
    }

    public function getLineNumber(): ?int
    {
        return $this->lineNumber;
    }

    public function hasSuggestions(): bool
    {
        return !empty($this->suggestions);
    }

    public function hasContext(): bool
    {
        return !empty($this->context);
    }

    /**
     * @return string[]
     */
    public function getSuggestions(): array
    {
        return $this->suggestions;
    }

    public function getContext(): array
    {
        return $this->context;
    }

    public function setContext(array $context): MisspellingInterface
    {
        return new self(
            $this->word,
            $this->offset,
            $this->lineNumber,
            $this->suggestions,
            $context
        );
    }

    public function mergeContext(array $context, bool $override = true): MisspellingInterface
    {
        if (empty($context)) {
            throw new InvalidArgumentException('Context trying to be merged is empty');
        }

        return new self(
            $this->word,
            $this->offset,
            $this->lineNumber,
            $this->suggestions,
            $override ? array_merge($this->context, $context) : array_merge($context, $this->context)
        );
    }
}
