<?php

declare(strict_types=1);

namespace PhpSpellcheck;

use PhpSpellcheck\Exception\InvalidArgumentException;
use PhpSpellcheck\MisspellingHandler\MisspellingHandlerInterface;
use PhpSpellcheck\Source\SourceInterface;
use PhpSpellcheck\Spellchecker\SpellcheckerInterface;
use PhpSpellcheck\TextProcessor\TextProcessorInterface;

class MisspellingFinder
{
    /**
     * @var SpellcheckerInterface
     */
    private $spellChecker;

    /**
     * @var MisspellingHandlerInterface|null
     */
    private $misspellingHandler;

    /**
     * @var TextProcessorInterface|null
     */
    private $textProcessor;

    public function __construct(
        SpellcheckerInterface $spellChecker,
        ?MisspellingHandlerInterface $misspellingHandler = null,
        ?TextProcessorInterface $textProcessor = null
    ) {
        $this->spellChecker = $spellChecker;
        $this->misspellingHandler = $misspellingHandler;
        $this->textProcessor = $textProcessor;
    }

    /**
     * @param iterable<TextInterface>|SourceInterface|string|TextInterface $source
     * @param array<mixed> $context
     * @param array<string> $languages
     *
     * @return MisspellingInterface[]
     */
    public function find(
        $source,
        array $languages = [],
        array $context = []
    ): iterable {
        if (\is_string($source)) {
            $texts = [new Text($source, $context)];
        } elseif ($source instanceof TextInterface) {
            $texts = [$source];
        } elseif (\is_array($source)) {
            $texts = $source;
        } elseif ($source instanceof SourceInterface) {
            $texts = $source->toTexts($context);
        } else {
            $sourceVarType = \is_object($source) ? \get_class($source) : \gettype($source);
            $allowedTypes = implode(' or ', ['"string"', '"' . SourceInterface::class . '"', '"iterable<' . TextInterface::class . '>"', '"' . TextInterface::class . '"']);

            throw new InvalidArgumentException('Source should be of type ' . $allowedTypes . ', "' . $sourceVarType . '" given');
        }

        $misspellings = $this->doSpellCheckTexts($texts, $languages);

        if ($this->misspellingHandler !== null) {
            $this->misspellingHandler->handle($misspellings);
        }

        return $misspellings;
    }

    public function setSpellchecker(SpellcheckerInterface $spellChecker): void
    {
        $this->spellChecker = $spellChecker;
    }

    public function setMisspellingHandler(MisspellingHandlerInterface $misspellingHandler): void
    {
        $this->misspellingHandler = $misspellingHandler;
    }

    /**
     * @param TextInterface[] $texts
     * @param string[] $languages
     *
     * @return iterable<MisspellingInterface>
     */
    private function doSpellCheckTexts(
        iterable $texts,
        array $languages
    ): iterable {
        foreach ($texts as $text) {
            if ($this->textProcessor !== null) {
                $text = $this->textProcessor->process($text);
            }

            yield from $this->spellChecker->check(
                $text->getContent(),
                $languages,
                $text->getContext()
            );
        }
    }
}
