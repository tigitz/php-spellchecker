<?php

declare(strict_types=1);

use PhpSpellcheck\Exception\InvalidArgumentException;
use PhpSpellcheck\Utils\LineAndOffset;
use PHPUnit\Framework\TestCase;

class LineAndOffsetTest extends TestCase
{
    public function testFindFromFirstCharacterOffset(): void
    {
        $text = <<<TEXT
First line
Second line
Third line
Last line
TEXT;

        // offset 0  = before first character of 1st line
        $this->assertSame(
            [1, 0],
            LineAndOffset::findFromFirstCharacterOffset($text, 0)
        );

        // offset 10  = after last character of 1st line
        $this->assertSame(
            [1, 10],
            LineAndOffset::findFromFirstCharacterOffset($text, 10)
        );

        // offset 11 = before first character of 2nd line
        $this->assertSame(
            [2, 0],
            LineAndOffset::findFromFirstCharacterOffset($text, 11)
        );
        // offset 23 = before first character of 3rd line
        $this->assertSame(
            [3, 0],
            LineAndOffset::findFromFirstCharacterOffset($text, 23)
        );
        // offset 24 = after first character of 3rd line
        $this->assertSame(
            [3, 1],
            LineAndOffset::findFromFirstCharacterOffset($text, 24)
        );

        // offset 34 = after first character of 4th line
        $this->assertSame(
            [4, 0],
            LineAndOffset::findFromFirstCharacterOffset($text, 34)
        );

        // offset 34 = after last character of 4th line
        $this->assertSame(
            [4, 9],
            LineAndOffset::findFromFirstCharacterOffset($text, 43)
        );
    }

    public function testThrowExceptionIfOffsetGivenIsHigherThanStringLength(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->assertSame(
            [4, 0],
            LineAndOffset::findFromFirstCharacterOffset('test', 50)
        );
    }

    public function testThrowExceptionIfOffsetisNegative(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->assertSame(
            [4, 0],
            LineAndOffset::findFromFirstCharacterOffset('test', -50)
        );
    }
}
