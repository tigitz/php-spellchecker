<?php

declare(strict_types=1);

namespace PhpSpellcheck;

use PhpSpellcheck\Exception\InvalidArgumentException;
use Symfony\Component\String\UnicodeString;

class Text extends UnicodeString implements TextInterface
{
    /**
     * @var array
     */
    private $context;

    public static function create(string $content, array $context = []): Text
    {
        return (new self($content))->setContext($context);
    }

    public function setContext(array $context): self
    {
        $this->context = $context;

        return $this;
    }

    public function getContent(): string
    {
        return (string) $this->string;
    }

    public function getContext(): array
    {
        return $this->context;
    }

    public function replaceContent(string $newContent): TextInterface
    {
        return self::create($newContent, $this->context);
    }

    public function mergeContext(array $context, bool $override = true): TextInterface
    {
        if (empty($context)) {
            throw new InvalidArgumentException('Context trying to be merged is empty');
        }

        return self::create(
            $this->getContent(),
            $override ? array_merge($this->getContext(), $context) : array_merge($context, $this->getContext())
        );
    }
}
