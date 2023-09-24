<?php

declare(strict_types=1);

namespace PhpSpellcheck\Utils;

use PhpSpellcheck\Misspelling;

class IspellParser
{
    /**
     * @param string $output assumed to be generated from an input on which {@see IspellParser::adaptInputForTerseModeProcessing()} has been applied
     * @param array<mixed> $context
     *
     * @return iterable<Misspelling>
     */
    public static function parseMisspellingsFromOutput(string $output, array $context = []): iterable
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
                        /**
                         * a `^` character is added to each line while sending text as input so it needs to
                         * account for that. {@see IspellParser::adaptInputForTerseModeProcessing}.
                         */
                        (int) trim($offset) - 1,
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
                        /**
                         * a `^` character is added to each line while sending text as input so it needs to
                         * account for that. {@see IspellParser::adaptInputForTerseModeProcessing}.
                         */
                        (int) trim($offset) - 1,
                        $lineNumber,
                        explode(', ', trim($parts[1])),
                        $context
                    );

                    break;
            }
        }
    }

    /**
     * Preprocess the source text so that aspell/ispell pipe mode instruction is ignored.
     *
     * In pipe mode some special characters at the beginning of the line are instructions for aspell/ispell
     * {@link http://aspell.net/man-html/Through-A-Pipe.html#Through-A-Pipe}.
     *
     * Spellchecker must not interpret them, so it escapes them using the ^ symbol.
     */
    public static function adaptInputForTerseModeProcessing(string $input): string
    {
        return \Safe\preg_replace('/^/m', '^', $input);
    }
}
