<?php

declare(strict_types=1);

namespace PhpSpellcheck;

class Text implements TextInterface
{
    /**
     * @var array<mixed>
     */
    private $context;

    /**
     * @var string
     */
    private $string;

    /**
     * @param array<mixed> $context
     */
    public function __construct(string $string, array $context)
    {
        $this->string = $string;
        $this->context = $context;
    }

    /**
     * @param array<mixed> $context
     */
    public function setContext(array $context): self
    {
        $this->context = $context;

        return $this;
    }

    public function getContent(): string
    {
        return $this->string;
    }

    public function getContext(): array
    {
        return $this->context;
    }

    public function replaceContent(string $newContent): TextInterface
    {
        return new self($newContent, $this->context);
    }

    public function mergeContext(array $context, bool $override = true): TextInterface
    {
        return new self(
            $this->getContent(),
            $override ? array_merge($this->getContext(), $context) : array_merge($context, $this->getContext())
        );
    }
}
