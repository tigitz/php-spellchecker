<?php

declare(strict_types=1);

namespace PhpSpellcheck\Utils;

use PhpSpellcheck\Exception\InvalidArgumentException;
use Webmozart\Assert\Assert;

/**
 * @see https://github.com/cheeaun/kohana-core/blob/master/classes/kohana/arr.php#L151
 */
class SortedNumericArrayNearestValueFinder
{

    public const FIND_LOWER = -1;
    public const FIND_DEFAULT = 0;
    public const FIND_HIGHER = 1;

    public static function findIndex(int $needle, array $haystack, int $mode = self::FIND_DEFAULT): int
    {
        Assert::notEmpty($haystack);
        $high = count($haystack);
        $low = 0;

        while ($high - $low > 1) {
            $probe = ($high + $low) / 2;

            Assert::integerish($haystack[$probe]);
            if ($haystack[$probe] < $needle) {
                $low = $probe;
            } else {
                $high = $probe;
            }
        }
        if ($high === count($haystack) or $haystack[$high] !== $needle) {
            if ($high === count($haystack)) {
                Assert::integerish($haystack[$high - 1]);

                return $high - 1;
            }

            $ceil_low = (int) ceil($low);
            $floor_low = (int) floor($low);
            $high_distance = $haystack[$ceil_low] - $needle;
            $low_distance = $needle - $haystack[$floor_low];

            if ($mode === self::FIND_LOWER) {
                return $floor_low;
            }

            if ($mode === self::FIND_HIGHER) {
                return $ceil_low;
            }

            if ($mode === self::FIND_DEFAULT) {
                return ($high_distance >= $low_distance) ? $ceil_low : $floor_low;
            }

            throw new InvalidArgumentException('Finding mode value "' . $mode . '" is not supported');
        }

        return (int) $high;
    }
}
