<?php

declare(strict_types=1);

use PhpSpellcheck\Source\File;
use PHPUnit\Framework\TestCase;
use Safe\Exceptions\FilesystemException;

use function PhpSpellcheck\t;

class FileTest extends TestCase
{
    private const TEXT_FIXTURE_FILE_PATH = __DIR__ . '/../Fixtures/Text/mispelling1.txt';

    public function testToTexts(): void
    {
        $texts = (new File(self::TEXT_FIXTURE_FILE_PATH))->toTexts(['ctx' => 'in tests']);
        $this->assertEquals(
            [
                t(
                    "mispelling1\n",
                    [
                        'ctx' => 'in tests',
                        'filePath' => realpath(self::TEXT_FIXTURE_FILE_PATH),
                    ]
                ),
            ],
            iterator_to_array($texts)
        );
    }

    public function testInvalidPath(): void
    {
        $this->expectException(FilesystemException::class);
        iterator_to_array((new File('invalidPath'))->toTexts());
    }
}
