<?php

declare(strict_types=1);

namespace PhpSpellcheck;

use PhpSpellcheck\Exception\InvalidArgumentException;
use PhpSpellcheck\MisspellingHandler\MisspellingHandlerInterface;
use PhpSpellcheck\Source\PHPString;
use PhpSpellcheck\Source\SourceInterface;
use PhpSpellcheck\Spellchecker\SpellcheckerInterface;
use PhpSpellcheck\TextProcessor\TextProcessorInterface;
use PhpSpellcheck\Utils\TextEncoding;

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
     * @param string|SourceInterface $source
     *
     * @return MisspellingInterface[]
     */
    public function find(
        $source,
        array $languages = [],
        array $context = [],
        string $spellCheckerEncoding = TextEncoding::UTF8
    ): iterable {
        $misspellings = $this->doSpellcheck($source, $languages, $context, $spellCheckerEncoding);

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
     * @param string|SourceInterface $source
     *
     * @return MisspellingInterface[]
     */
    private function doSpellcheck($source, array $languages, array $context, string $spellCheckEncoding): iterable
    {
        if (is_string($source)) {
            $source = new PHPString($source);
        }

        if ($source instanceof SourceInterface) {
            return $this->doSpellcheckFromSource($source, $languages, $context, $spellCheckEncoding);
        }

        $sourceVarType = is_object($source) ? get_class($source) : gettype($source);

        throw new InvalidArgumentException('Source should be of type string or ' . SourceInterface::class . '. "' . $sourceVarType . '" given');
    }

    /**
     * @return MisspellingInterface[]
     */
    private function doSpellcheckFromSource(
        SourceInterface $source,
        array $languages,
        array $context,
        string $spellCheckEncoding
    ): iterable {
        foreach ($source->toTexts($context) as $text) {
            if ($this->textProcessor !== null) {
                $text = $this->textProcessor->process($text);
            }

            $misspellingsCheck = $this->spellChecker->check(
                $text->getContent(),
                $languages,
                $text->getContext(),
                $spellCheckEncoding
            );

            yield from $misspellingsCheck;
        }
    }
}
