<?php

declare(strict_types=1);

namespace PhpSpellcheck\Utils;

use PhpSpellcheck\Misspelling;

class IspellOutputParser
{
    /**
     * @param array<mixed> $context
     *
     * @return iterable<Misspelling>
     */
    public static function parseMisspellings(string $output, array $context = []): iterable
    {
        $lines = explode(PHP_EOL, $output);
        $lineNumber = 1;
        foreach ($lines as $line) {
            $line = trim($line);
            if ('' === $line) {
                ++$lineNumber;
                // Go to the next line
                continue;
            }

            switch ($line[0]) {
                case '#':
                    [, $word, $offset] = explode(' ', $line);
                    yield new Misspelling(
                        $word,
                        (int) trim($offset),
                        $lineNumber,
                        [],
                        $context
                    );

                    break;
                case '&':
                    $parts = explode(':', $line);
                    [, $word, , $offset] = explode(' ', $parts[0]);
                    yield new Misspelling(
                        $word,
                        (int) trim($offset),
                        $lineNumber,
                        explode(', ', trim($parts[1])),
                        $context
                    );

                    break;
            }
        }
    }
}
