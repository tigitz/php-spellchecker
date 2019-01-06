<?php

declare(strict_types=1);

namespace PhpSpellcheck\Tests\Source;

use PhpSpellcheck\Source\Directory;
use PhpSpellcheck\Text;
use PhpSpellcheck\Utils\TextEncoding;
use PHPUnit\Framework\TestCase;

class DirectoryTest extends TestCase
{
    private const TEXT_FIXTURES_PATH = __DIR__ . '/../Fixtures/Text/Directory';

    public function testToTexts()
    {
        $textsFromDirectory = (new Directory(self::TEXT_FIXTURES_PATH))->toTexts(['ctx' => 'in tests']);
        $expectedValues = [
            new Text(
                "mispélling3\n",
                TextEncoding::UTF8,
                ['ctx' => 'in tests', 'filePath' => realpath(self::TEXT_FIXTURES_PATH . '/mispelling3.txt')]
            ),
            new Text(
                "mispelling2\n",
                TextEncoding::ASCII,
                ['ctx' => 'in tests', 'filePath' => realpath(self::TEXT_FIXTURES_PATH . '/mispelling2.txt')]
            ),
            new Text(
                "mispelling4\n",
                TextEncoding::ASCII,
                [
                'ctx' => 'in tests',
                    'filePath' => realpath(self::TEXT_FIXTURES_PATH . '/SubDirectory/mispelling4.txt'),
                ]
            ),
        ];
        $realValues = iterator_to_array($textsFromDirectory);

        foreach ($expectedValues as $value) {
            $this->assertTrue(in_array($value, $realValues));
        }
    }

    public function testToTextsMatchingRegex()
    {
        $textsFromDirectory = (new Directory(self::TEXT_FIXTURES_PATH, '/^((?!mispelling3\.txt).)*$/'))
            ->toTexts(['ctx' => 'in tests']);

        $expectedValues = [
            new Text(
                "mispelling2\n",
                TextEncoding::ASCII,
                ['ctx' => 'in tests', 'filePath' => realpath(self::TEXT_FIXTURES_PATH . '/mispelling2.txt')]
            ),
            new Text("mispelling4\n", TextEncoding::ASCII, [
                'ctx' => 'in tests',
                'filePath' => realpath(self::TEXT_FIXTURES_PATH . '/SubDirectory/mispelling4.txt'),
            ]),
        ];
        $realValues = iterator_to_array($textsFromDirectory);

        foreach ($expectedValues as $value) {
            $this->assertTrue(in_array($value, $realValues));
        }
    }
}
