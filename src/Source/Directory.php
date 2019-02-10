<?php

declare(strict_types=1);

namespace PhpSpellcheck\Source;

use PhpSpellcheck\Text;

class Directory implements SourceInterface
{
    /**
     * @var string
     */
    private $dirPath;

    /**
     * @var null|string
     */
    private $pattern;

    public function __construct(string $dirPath, ?string $pattern = null)
    {
        $this->dirPath = $dirPath;
        $this->pattern = $pattern;
    }

    /**
     * @return Text[]
     */
    private function getContents(): iterable
    {
        $filesInDir = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(
                $this->dirPath,
                \FilesystemIterator::SKIP_DOTS | \FilesystemIterator::CURRENT_AS_PATHNAME
            ),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        if ($this->pattern) {
            $filesInDir = new \RegexIterator($filesInDir, $this->pattern, \RegexIterator::GET_MATCH);
        }

        /** @var \SplFileInfo|string|array $file */
        foreach ($filesInDir as $file) {
            if (is_string($file)) {
                $file = new \SplFileInfo($file);
            } elseif (is_array($file)) {
                // When regex pattern is used, an array containing the file path in its first element is returned
                $file = new \SplFileInfo(current($file));
            }

            if (!$file->isDir()) {
                if ($file->getRealPath() !== false) {
                    yield current((new File($file->getRealPath()))->toTexts());
                }
            }
        }
    }

    public function toTexts(array $context): iterable
    {
        foreach ($this->getContents() as $text) {
            yield new Text(
                $text->getContent(),
                $text->getEncoding(),
                array_merge($text->getContext(), $context)
            );
        }
    }
}
