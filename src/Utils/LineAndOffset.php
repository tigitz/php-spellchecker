<?php

declare(strict_types=1);

namespace PhpSpellcheck\Utils;

use PhpSpellcheck\Exception\InvalidArgumentException;
use Webmozart\Assert\Assert;

class LineAndOffset
{
    /**
     * When spellcheckers gives the offset position of a misspelled word from the whole text's first character,
     * this helps finding the offset position from line's first caracter instead.
     *
     * @param string $text Chunk of text from which the line and offset are computed
     * @param int $offsetFromFirstCharacter Offset position from the text's first caracter
     *
     * @return array<int,int> Line number as the first element and offset from beginning of line as second element
     */
    public static function findFromFirstCharacterOffset(string $text, int $offsetFromFirstCharacter): array
    {
        // positive offset
        Assert::greaterThanEq($offsetFromFirstCharacter, 0, \Safe\sprintf('Offset must be a positive integer, "%s" given', $offsetFromFirstCharacter));

        $textLength = mb_strlen($text);
        if ($textLength < $offsetFromFirstCharacter) {
            throw new InvalidArgumentException(
                \Safe\sprintf('Offset given "%d" is higher than the string length "%d"', $offsetFromFirstCharacter, $textLength)
            );
        }

        $textBeforeOffset = mb_substr($text, 0, $offsetFromFirstCharacter);
        $line = ((int) \Safe\preg_match_all('/\R/u', $textBeforeOffset, $matches)) + 1;
        $offsetOfPreviousLinebreak = mb_strrpos($textBeforeOffset, PHP_EOL, 0);

        $offset = $offsetFromFirstCharacter - ($offsetOfPreviousLinebreak !== false ? $offsetOfPreviousLinebreak + 1 : 0);

        return [$line, $offset];
    }
}
