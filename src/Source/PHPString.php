<?php

declare(strict_types=1);

namespace PhpSpellcheck\Source;

use PhpSpellcheck\Exception\RuntimeException;
use PhpSpellcheck\Text;
use PhpSpellcheck\TextInterface;

class PHPString implements SourceInterface
{
    /**
     * @var string
     */
    private $string;

    public function __construct(string $string)
    {
        $this->string = $string;
    }

    /**
     * @param array $context
     *
     * @return TextInterface[]
     */
    public function toTexts(array $context): iterable
    {
        $encoding = mb_detect_encoding($this->string, null, true);

        if ($encoding === false) {
            throw new RuntimeException(
                \Safe\sprintf(
                    'Coulnd\'t detect enconding of string:' . PHP_EOL . '%s',
                    $this->string
                )
            );
        }

        yield new Text($this->string, $encoding, $context);
    }
}
