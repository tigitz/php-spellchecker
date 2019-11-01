<?php

declare(strict_types=1);

namespace PhpSpellcheck\Tests\Exception;

use PhpSpellcheck\Exception\ProcessHasErrorOutputException;
use PHPUnit\Framework\TestCase;

class ProcessHasErrorOutputExceptionTest extends TestCase
{
    public function testException(): void
    {
        $exception = new ProcessHasErrorOutputException('error output', 'testt', 'ispell --encoding=utf-8 -a');
        $this->assertSame(<<<MESSAGE
Process has generated the following output errors:

error output

With command: "ispell --encoding=utf-8 -a"

For text:
"testt"
MESSAGE
            , $exception->getMessage());
    }
}
