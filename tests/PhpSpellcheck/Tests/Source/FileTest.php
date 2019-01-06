<?php

declare(strict_types=1);

namespace PhpSpellcheck\Tests\Source;

use PhpSpellcheck\Source\File;
use PhpSpellcheck\Text;
use PhpSpellcheck\Utils\TextEncoding;
use PHPUnit\Framework\TestCase;
use Safe\Exceptions\FilesystemException;

class FileTest extends TestCase
{
    private const TEXT_FIXTURE_FILE_PATH = __DIR__ . '/../Fixtures/Text/mispelling1.txt';

    public function testToTexts()
    {
        $texts = (new File(self::TEXT_FIXTURE_FILE_PATH))->toTexts(['ctx' => 'in tests']);
        $this->assertEquals(
            [
                new Text(
                    "mispelling1\n",
                    TextEncoding::ASCII,
                    [
                        'ctx' => 'in tests',
                        'filePath' => realpath(self::TEXT_FIXTURE_FILE_PATH),
                    ]
                ),
            ],
            $texts
        );
    }

    public function testInvalidPath()
    {
        $this->expectException(FilesystemException::class);
        (new File('invalidPath'))->toTexts();
    }

    public function testToTextsWithEncoding()
    {
        $texts = (new File(self::TEXT_FIXTURE_FILE_PATH, TextEncoding::UTF8))->toTexts(['ctx' => 'in tests']);
        $this->assertEquals(
            [
                new Text(
                    "mispelling1\n",
                    TextEncoding::UTF8,
                    [
                        'ctx' => 'in tests',
                        'filePath' => realpath(self::TEXT_FIXTURE_FILE_PATH),
                    ]
                ),
            ],
            $texts
        );
    }
}
