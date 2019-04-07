<?php

declare(strict_types=1);

namespace PhpSpellcheck\Source;

use PhpSpellcheck\Text;
use PhpSpellcheck\Utils\TextEncoding;

class File implements SourceInterface
{
    /**
     * @var string
     */
    private $filePath;

    /**
     * @var string|null
     */
    private $encoding;

    public function __construct(string $filePath, ?string $encoding = null)
    {
        $this->filePath = $filePath;
        $this->encoding = $encoding;
    }

    public function toTexts(array $context = []): iterable
    {
        $context['filePath'] = \Safe\realpath($this->filePath);
        $encoding = $this->encoding ?? TextEncoding::detect($this->getFileContent());

        yield new Text(
            $this->getFileContent(),
            $encoding,
            $context
        );
    }

    private function getFileContent(): string
    {
        return \Safe\file_get_contents($this->filePath);
    }
}
