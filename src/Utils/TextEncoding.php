<?php

declare(strict_types=1);

namespace PhpSpellcheck\Utils;

use PhpSpellcheck\Exception\RuntimeException;

/**
 * Manage different text encoding behavior.
 *
 * @TODO This class is meant to be an enum implementing the list of the most common character encodings
 *
 * @see https://stackoverflow.com/a/8528866
 */
class TextEncoding
{
    public const UTF8 = 'UTF-8';
    public const ASCII = 'ASCII';

    public static function detect(string $string): string
    {
        $encoding = mb_detect_encoding($string, null, true);

        if ($encoding === false) {
            throw new RuntimeException(
                \Safe\sprintf(
                    'Coulnd\'t detect encoding of string:' . PHP_EOL . '%s',
                    $string
                )
            );
        }

        return $encoding;
    }
}
