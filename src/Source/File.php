<?php

declare(strict_types=1);

namespace PhpSpellcheck\Source;

use PhpSpellcheck\Exception\RuntimeException;
use PhpSpellcheck\Text;

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
        $encoding = $this->encoding;

        if ($encoding === null) {
            $encoding = mb_detect_encoding($this->getFileContent(), null, true);

            if ($encoding === false) {
                throw new RuntimeException(
                    \Safe\sprintf(
                        'Coulnd\'t detect enconding of string:' . PHP_EOL . '%s',
                        $this->getFileContent()
                    )
                );
            }
        }

        return [
            new Text(
                $this->getFileContent(),
                $encoding,
                $context
            ),
        ];
    }

    private function getFileContent(): string
    {
        return \Safe\file_get_contents($this->filePath);
    }
}
