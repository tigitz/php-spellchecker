<?php

declare(strict_types=1);

namespace PhpSpellcheck\Source;

use Webmozart\Assert\Assert;

class MultiSource implements SourceInterface
{
    /**
     * @var iterable<SourceInterface>
     */
    private $sources;

    /**
     * @param iterable<SourceInterface> $sources
     */
    public function __construct(iterable $sources)
    {
        Assert::allIsInstanceOf($sources, SourceInterface::class);
        $this->sources = $sources;
    }

    public function toTexts(array $context = []): iterable
    {
        foreach ($this->sources as $source) {
            foreach ($source->toTexts($context) as $text) {
                yield $text->mergeContext($context, true);
            }
        }
    }
}
