<?php

declare(strict_types=1);

namespace PhpSpellcheck\Tests;

use PhpSpellcheck\Exception\InvalidArgumentException;
use PhpSpellcheck\Text;
use PHPUnit\Framework\TestCase;

class TextTest extends TestCase
{
    public const CONTENT_STUB = <<<EOT
Tigr, tiger, burning страх.
In theforests of the night,
What imortal hand or eey
CCould frame thy fearful symmetry?
EOT;

    public function testContextOverridingMerge()
    {
        $text = Text::utf8('test', ['idx' => '1'])->mergeContext(['idx' => 'foo', 'idx2' => '2']);

        $this->assertEquals(Text::utf8('test', ['idx' => 'foo', 'idx2' => '2']), $text);
    }

    public function testContextNonOverridingMerge()
    {
        $text = Text::utf8('test', ['idx' => '1'])->mergeContext(['idx' => 'foo', 'idx2' => '2'], false);

        $this->assertEquals(Text::utf8('test', ['idx' => '1', 'idx2' => '2']), $text);
    }

    public function testExceptionWhenMergingEmptyContext()
    {
        $this->expectException(InvalidArgumentException::class);
        Text::utf8('test', ['idx' => '1'])->mergeContext([]);
    }
}
