<?php

declare(strict_types=1);

namespace PhpSpellcheck\Tests\Utils;

use PhpSpellcheck\Utils\SortedNumericArrayNearestValueFinder;
use PHPUnit\Framework\TestCase;

class SortedNumericArrayNearestValueFinderTest extends TestCase
{

    public function testFindLowerOrHigher()
    {
        $foundNearestLowerIndex = SortedNumericArrayNearestValueFinder::findIndex(
            1337,
            [1000, 2000, 3000],
            SortedNumericArrayNearestValueFinder::FIND_LOWER
        );
        $this->assertSame(0, $foundNearestLowerIndex);

        $foundNearestLowerIndex = SortedNumericArrayNearestValueFinder::findIndex(
            1337,
            [1000, 2000, 3000],
            SortedNumericArrayNearestValueFinder::FIND_HIGHER
        );
        $this->assertSame(1, $foundNearestLowerIndex);

        $foundNearestLowerIndex = SortedNumericArrayNearestValueFinder::findIndex(
            2000,
            [1000, 2000, 3000],
            SortedNumericArrayNearestValueFinder::FIND_LOWER
        );
        $this->assertSame(1, $foundNearestLowerIndex);

        $foundNearestLowerIndex = SortedNumericArrayNearestValueFinder::findIndex(
            1500,
            [1000, 2000, 3000],
            SortedNumericArrayNearestValueFinder::FIND_DEFAULT
        );
        $this->assertSame(1, $foundNearestLowerIndex);

        $foundNearestLowerIndex = SortedNumericArrayNearestValueFinder::findIndex(
            1337,
            [1000, 2000, 3000],
            SortedNumericArrayNearestValueFinder::FIND_DEFAULT
        );
        $this->assertSame(1, $foundNearestLowerIndex);

        $foundNearestLowerIndex = SortedNumericArrayNearestValueFinder::findIndex(
            4000,
            [1000, 2000, 3000],
            SortedNumericArrayNearestValueFinder::FIND_DEFAULT
        );
        $this->assertSame(2, $foundNearestLowerIndex);

        $foundNearestLowerIndex = SortedNumericArrayNearestValueFinder::findIndex(
            4000,
            [1000, 2000, 3000],
            SortedNumericArrayNearestValueFinder::FIND_HIGHER
        );
        $this->assertSame(2, $foundNearestLowerIndex);

        $foundNearestLowerIndex = SortedNumericArrayNearestValueFinder::findIndex(
            50,
            [1000, 2000, 3000],
            SortedNumericArrayNearestValueFinder::FIND_DEFAULT
        );
        $this->assertSame(0, $foundNearestLowerIndex);

        $foundNearestLowerIndex = SortedNumericArrayNearestValueFinder::findIndex(
            50,
            [1000, 2000, 3000],
            SortedNumericArrayNearestValueFinder::FIND_LOWER
        );
        $this->assertSame(0, $foundNearestLowerIndex);
    }

    public function testFindEmptyArray()
    {
        $this->expectException(\InvalidArgumentException::class);

        SortedNumericArrayNearestValueFinder::findIndex(
            1500,
            [],
            SortedNumericArrayNearestValueFinder::FIND_DEFAULT
        );
    }

    public function testFindInNonIntArray()
    {
        $this->expectException(\InvalidArgumentException::class);

        SortedNumericArrayNearestValueFinder::findIndex(
            1500,
            ['foo'],
            SortedNumericArrayNearestValueFinder::FIND_DEFAULT
        );
    }

    public function testFindInMixedTypedArray()
    {
        $this->expectException(\InvalidArgumentException::class);

        SortedNumericArrayNearestValueFinder::findIndex(
            1500,
            ['foo', 1, 'bar'],
            SortedNumericArrayNearestValueFinder::FIND_DEFAULT
        );
    }
}
