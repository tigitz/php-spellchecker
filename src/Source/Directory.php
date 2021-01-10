<?php

declare(strict_types=1);

namespace PhpSpellcheck\Source;

use PhpSpellcheck\Exception\RuntimeException;
use PhpSpellcheck\Text;

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

    /**
     * @param array<mixed> $context
     *
     * @return iterable<Text>
     */
    public function toTexts(array $context): iterable
    {
        foreach ($this->getContents() as $text) {
            yield new Text($text->getContent(), array_merge($text->getContext(), $context));
        }
    }

    /**
     * @return iterable<Text>
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

        if ($this->pattern !== null) {
            $filesInDir = new \RegexIterator($filesInDir, $this->pattern, \RegexIterator::GET_MATCH);
        }

        /** @var array<string>|\SplFileInfo|string $file */
        foreach ($filesInDir as $file) {
            if (\is_string($file)) {
                $file = new \SplFileInfo($file);
            } elseif (\is_array($file) && !empty($file)) {
                // When regex pattern is used, an array containing the file path in its first element is returned
                $file = new \SplFileInfo(current($file));
            } else {
                throw new RuntimeException(\Safe\sprintf('Couldn\'t create "%s" object from the given file', \SplFileInfo::class));
            }

            if (!$file->isDir() && $file->getRealPath() !== false) {
                yield from (new File($file->getRealPath()))->toTexts();
            }
        }
    }
}
