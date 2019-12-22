<?php

declare(strict_types=1);

namespace PhpSpellcheck\Source;

class Directory implements SourceInterface
{
    /**
     * @var string
     */
    private $dirPath;

    /**
     * @var string|null
     */
    private $pattern;

    public function __construct(string $dirPath, ?string $pattern = null)
    {
        $this->dirPath = $dirPath;
        $this->pattern = $pattern;
    }

    public function toTexts(array $context): iterable
    {
        foreach ($this->getContents() as $text) {
            yield t($text->getContent(), array_merge($text->getContext(), $context));
        }
    }

    /**
     * @return \Generator<TextInterface>
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

        /** @var array|\SplFileInfo|string $file */
        foreach ($filesInDir as $file) {
            if (is_string($file)) {
                $file = new \SplFileInfo($file);
            } elseif (is_array($file)) {
                // When regex pattern is used, an array containing the file path in its first element is returned
                $file = new \SplFileInfo(current($file));
            }

            if (!$file->isDir() && $file->getRealPath() !== false) {
                yield from (new File($file->getRealPath()))->toTexts();
            }
        }
    }
}
