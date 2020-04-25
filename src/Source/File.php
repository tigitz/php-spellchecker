<?php

declare(strict_types=1);

namespace PhpSpellcheck\Source;

use PhpSpellcheck\Text;

class File implements SourceInterface
{
    /**
     * @var string
     */
    private $filePath;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * @param array<mixed> $context
     *
     * @return iterable<Text>
     */
    public function toTexts(array $context = []): iterable
    {
        $context['filePath'] = \Safe\realpath($this->filePath);

        yield new Text($this->getFileContent(), $context);
    }

    private function getFileContent(): string
    {
        return \Safe\file_get_contents($this->filePath);
    }
}
