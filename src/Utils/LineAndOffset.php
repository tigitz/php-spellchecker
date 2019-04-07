<?php

declare(strict_types=1);

namespace PhpSpellcheck\Utils;

use PhpSpellcheck\Exception\InvalidArgumentException;
use Webmozart\Assert\Assert;

class LineAndOffset
{
    /**
     * When spellcheckers only gives the offset position of a word from the first character of the whole text
     * and not from the first character of the word's line, this function helps computing it anyway.
     *
     * @return array(int,int) Line number as the first element and offset from begining of line as second element
     */
    public static function findFromFirstCharacterOffset(string $text, int $offsetFromFirstCharacter, string $encoding = TextEncoding::UTF8): array
    {
        // positive offset
        Assert::greaterThanEq($offsetFromFirstCharacter, 0, 'Offset must be positive');

        $textLength = mb_strlen($text);
        if ($textLength < $offsetFromFirstCharacter) {
            throw new InvalidArgumentException(
                \Safe\sprintf('Offset given "%d" is higher than the string length "%d"', $offsetFromFirstCharacter, $textLength)
            );
        }

        $textBeforeOffset = mb_substr($text, 0, $offsetFromFirstCharacter, $encoding);
        $line = mb_substr_count($textBeforeOffset, PHP_EOL) + 1;
        $offsetOfPreviousLinebreak = mb_strrpos($textBeforeOffset, PHP_EOL, 0, $encoding);

        $offset = $offsetFromFirstCharacter - ($offsetOfPreviousLinebreak !== false ? $offsetOfPreviousLinebreak + 1 : 0);

        return [$line, $offset];
    }
}
