<?php

declare(strict_types=1);

use PhpSpellcheck\Source\Directory;
use PHPUnit\Framework\TestCase;

class DirectoryTest extends TestCase
{
    private const TEXT_FIXTURES_PATH = __DIR__ . '/../Fixtures/Text/Directory';

    public function testToTexts(): void
    {
        $textsFromDirectory = (new Directory(self::TEXT_FIXTURES_PATH))->toTexts(['ctx' => 'in tests']);
        $expectedValues = [
            t("mispélling3\n", ['ctx' => 'in tests', 'filePath' => realpath(self::TEXT_FIXTURES_PATH . '/mispelling3.txt')]),
            t(
                "mispelling2\n",
                ['ctx' => 'in tests', 'filePath' => realpath(self::TEXT_FIXTURES_PATH . '/mispelling2.txt')]
            ),
            t(
                "mispelling4\n",
                [
                    'ctx' => 'in tests',
                    'filePath' => realpath(self::TEXT_FIXTURES_PATH . '/SubDirectory/mispelling4.txt'),
                ]
            ),
        ];
        $realValues = iterator_to_array($textsFromDirectory);

        foreach ($expectedValues as $value) {
            $this->assertContainsEquals($value, $realValues);
        }
    }

    public function testToTextsMatchingRegex(): void
    {
        $textsFromDirectory = (new Directory(self::TEXT_FIXTURES_PATH, '/^((?!mispelling3\.txt).)*$/'))
            ->toTexts(['ctx' => 'in tests']);

        $expectedValues = [
            t(
                "mispelling2\n",
                ['ctx' => 'in tests', 'filePath' => realpath(self::TEXT_FIXTURES_PATH . '/mispelling2.txt')]
            ),
            t("mispelling4\n", [
                'ctx' => 'in tests',
                'filePath' => realpath(self::TEXT_FIXTURES_PATH . '/SubDirectory/mispelling4.txt'),
            ]),
        ];
        $realValues = iterator_to_array($textsFromDirectory);

        foreach ($expectedValues as $value) {
            $this->assertContainsEquals($value, $realValues);
        }
    }
}
