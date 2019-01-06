<?php

declare(strict_types=1);

namespace PhpSpellcheck\Tests\Source;

use PhpSpellcheck\Source\MultipleSource;
use PhpSpellcheck\Source\SourceInterface;
use PhpSpellcheck\TextInterface;
use PhpSpellcheck\Utils\TextEncoding;
use PHPUnit\Framework\TestCase;

class MultipleSourceTest extends TestCase
{

    public function testToTexts()
    {
        $mockText1 = $this->generateMockText('mispelling1', ['ctx' => null]);
        $mockText1AfterContextMerge = $this->generateMockText('mispelling1AfterMerge', ['ctx' => 'merged']);
        $mockText1->method('mergeContext')
            ->willReturn($mockText1AfterContextMerge);
        $mockText2 = $this->generateMockText('mispelling2');
        $mockText2->method('mergeContext')
            ->willReturn($mockText2);
        $mockSource1 = $this->generateMockSource([$mockText1]);
        $mockSource2 = $this->generateMockSource([$mockText2]);

        $source = new MultipleSource(
            [
                $mockSource1,
                $mockSource2,
            ]
        );

        $expectedTexts = [$mockText1AfterContextMerge, $mockText2];

        $this->assertSame($expectedTexts, iterator_to_array($source->toTexts()));
    }

    private function generateMockText(string $content, array $context = [])
    {
        $textMock = $this->createMock(TextInterface::class);
        $textMock->method('getContext')
            ->willReturn($context);
        $textMock->method('getEncoding')
            ->willReturn(TextEncoding::UTF8);
        $textMock->method('getContent')
            ->willReturn($content);

        return $textMock;
    }

    private function generateMockSource(array $texts)
    {
        $sourceMock = $this->createMock(SourceInterface::class);
        $sourceMock->expects($this->once())
            ->method('toTexts')
            ->willReturn($texts);

        return $sourceMock;
    }
}
