<?php

declare(strict_types=1);

namespace PhpSpellcheck\MisspellingHandler;

use PhpSpellcheck\MisspellingInterface;

class EchoHandler implements MisspellingHandlerInterface
{
    /**
     * @param MisspellingInterface[] $misspellings
     */
    public function handle(iterable $misspellings): void
    {
        foreach ($misspellings as $misspelling) {
            $output = \Safe\sprintf(
                'word: %s | line: %d | offset: %d | suggestions: %s | context: %s' . PHP_EOL,
                $misspelling->getWord(),
                $misspelling->getLineNumber(),
                $misspelling->getOffset(),
                $misspelling->hasSuggestions() ? implode(',', $misspelling->getSuggestions()) : '',
                \Safe\json_encode($misspelling->getContext())
            );

            echo $output;
        }
    }
}
