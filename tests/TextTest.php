<?php

declare(strict_types=1);

namespace PhpSpellcheck\Tests;

use PHPUnit\Framework\TestCase;
use function PhpSpellcheck\t;

class TextTest extends TestCase
{
    public const CONTENT_STUB = <<<TEXT
Tigr, tiger, burning страх.
In theforests of the night,
What imortal hand or eey
CCould frame thy fearful symmetry?
TEXT;

    public const CONTENT_STUB_JP = <<<TEXT
さよなら解決なる

さよなら

解決なる
TEXT;

    public const CONTENT_STUB_RU = <<<TEXT
Используйте этот инструмент для обнаружения опечаток, граматических и стилистических ошибок в англиских текстах.
Пример (с ошибками:
наведите мышь на подсвечиваемые слова, чтобы просмотреть опсание и, при наличии, варианта исправления ошибки.
Для проверки собственного текста, щёлкните на текстовое поле , вставьте свой текст и нажмите на кнопку "Отправить".
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
