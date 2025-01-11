<?php

declare(strict_types=1);

use PhpSpellcheck\Exception\InvalidArgumentException;
use PhpSpellcheck\Misspelling;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class MisspellingTest extends TestCase
{
    public function testMergeSuggestions(): void
    {
        $misspelling = new Misspelling('mispelled', 1, 0, ['misspelled']);
        $misspelling->mergeSuggestions(['misspelling', 'misspelled']);

        $this->assertSame(['misspelled', 'misspelling'], $misspelling->getSuggestions());
    }

    #[DataProvider('nonDeterminableUniqueIdentityMisspellings')]
    public function testCanDeterminateUniqueIdentity(Misspelling $misspelling): void
    {
        $this->assertFalse($misspelling->canDeterminateUniqueIdentity());
    }

    public static function nonDeterminableUniqueIdentityMisspellings(): array
    {
        return [
            [new Misspelling('mispelled')],
            [new Misspelling('mispelled', 1)],
            [new Misspelling('mispelled', null, 1)],
        ];
    }

    public function testContextOverridingMerge(): void
    {
        $misspelling = (new Misspelling('mispelled', 1, 0, [], ['idx' => '1']))->mergeContext([
            'idx' => 'foo',
            'idx2' => '2',
        ]);

        $this->assertEquals(new Misspelling('mispelled', 1, 0, [], ['idx' => 'foo', 'idx2' => '2']), $misspelling);
    }

    public function testContextNonOverridingMerge(): void
    {
        $misspelling = (new Misspelling('mispelled', 1, 0, [], ['idx' => '1']))->mergeContext([
            'idx' => 'foo',
            'idx2' => '2',
        ], false);

        $this->assertEquals(new Misspelling('mispelled', 1, 0, [], ['idx' => '1', 'idx2' => '2']), $misspelling);
    }

    public function testExceptionWhenMergingEmptyContext(): void
    {
        $this->expectException(InvalidArgumentException::class);
        (new Misspelling('mispelled', 1, 0, [], []))->mergeContext([]);
    }

    public function testImmutableSetContext(): void
    {
        $misspelling = new Misspelling('mispelled', 1, 0, [], []);
        $misspellingAfterSettingContext = $misspelling->setContext(['test']);

        $this->assertNotSame($misspelling, $misspellingAfterSettingContext);
    }
}
