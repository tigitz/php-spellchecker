<?php

declare(strict_types=1);

namespace PhpSpellcheck;

use PhpSpellcheck\Exception\InvalidArgumentException;
use PhpSpellcheck\Utils\TextEncoding;

class Text implements TextInterface
{
    /**
     * @var string
     */
    private $content;

    /**
     * @var string
     */
    private $encoding;

    /**
     * @var array
     */
    private $context;

    public function __construct(string $content, string $encoding, array $context = [])
    {
        $this->content = $content;
        $this->encoding = $encoding;
        $this->context = $context;
    }

    public function __toString()
    {
        return $this->getContent();
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getEncoding(): string
    {
        return $this->encoding;
    }

    public function getContext(): array
    {
        return $this->context;
    }

    public function replaceContent(string $newContent): TextInterface
    {
        return new self($newContent, $this->encoding, $this->context);
    }

    public function mergeContext(array $context, bool $override = true): TextInterface
    {
        return new self(
            $this->getContent(),
            $this->getEncoding(),
            $override ? array_merge($this->getContext(), $context) : array_merge($context, $this->getContext())
        );
    }

    public static function utf8(string $text, array $context = []): self
    {
        return new self($text, TextEncoding::UTF8, $context);
    }
}
