<?php

declare(strict_types=1);

namespace PhpSpellcheck\Tests;

use PHPUnit\Framework\TestCase;

class TextTest extends TestCase
{
    public const CONTENT_STUB = <<<TEXT
Tigr, tiger, burning страх.
In theforests of the night,
What imortal hand or eey
CCould frame thy fearful symmetry?
TEXT;

    public const CONTENT_STUB_MULTIBYTE = <<<TEXT
さよなら解決なる

さよなら

解決なる 
TEXT;

    public function testContextOverridingMerge(): void
    {
        $text = t('test', ['idx' => '1'])->mergeContext(['idx' => 'foo', 'idx2' => '2']);

        $this->assertEquals(t('test', ['idx' => 'foo', 'idx2' => '2']), $text);
    }

    public function testContextNonOverridingMerge(): void
    {
        $text = t('test', ['idx' => '1'])->mergeContext(['idx' => 'foo', 'idx2' => '2'], false);

        $this->assertEquals(t('test', ['idx' => '1', 'idx2' => '2']), $text);
    }
}
