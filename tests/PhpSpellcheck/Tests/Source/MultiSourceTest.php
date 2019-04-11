<?php

declare(strict_types=1);

namespace PhpSpellcheck\Tests\Source;

use PhpSpellcheck\Source\MultiSource;
use PhpSpellcheck\Source\SourceInterface;
use PhpSpellcheck\TextInterface;
use PhpSpellcheck\Utils\TextEncoding;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class MultiSourceTest extends TestCase
{
    public function testToTexts(): void
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

        $source = new MultiSource(
            [
                $mockSource1,
                $mockSource2,
            ]
        );

        $expectedTexts = [$mockText1AfterContextMerge, $mockText2];

        $this->assertSame($expectedTexts, iterator_to_array($source->toTexts()));
    }

    /**
     * @return MockObject|TextInterface
     */
    private function generateMockText(string $content, array $context = []): MockObject
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

    /**
     * @return MockObject|SourceInterface
     */
    private function generateMockSource(array $texts): MockObject
    {
        $sourceMock = $this->createMock(SourceInterface::class);
        $sourceMock->expects($this->once())
            ->method('toTexts')
            ->willReturn($texts);

        return $sourceMock;
    }
}
