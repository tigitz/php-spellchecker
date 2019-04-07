<?php

declare(strict_types=1);

namespace PhpSpellcheck\Source;

use PhpSpellcheck\Text;
use PhpSpellcheck\TextInterface;
use PhpSpellcheck\Utils\TextEncoding;

class PHPString implements SourceInterface
{
    /**
     * @var string
     */
    private $string;

    /**
     * @var string|null
     */
    private $encoding;

    public function __construct(string $string, ?string $encoding = null)
    {
        $this->string = $string;
        $this->encoding = $encoding;
    }

    /**
     * @return TextInterface[]
     */
    public function toTexts(array $context): iterable
    {
        $encoding = $this->encoding ?? TextEncoding::detect($this->string);

        yield new Text($this->string, $encoding, $context);
    }
}
