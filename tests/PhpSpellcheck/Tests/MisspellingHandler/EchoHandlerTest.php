<?php

declare(strict_types=1);

namespace PhpSpellcheck\Tests\MisspellingHandler;

use PhpSpellcheck\Misspelling;
use PhpSpellcheck\MisspellingHandler\EchoHandler;
use PHPUnit\Framework\TestCase;

class EchoHandlerTest extends TestCase
{
    public function testHandle(): void
    {
        $this->expectOutputString(
            'word: mispelling | line: 10 | offset: 4 | suggestions: misspelling,misspellings | context: {"sentence":"two mispelling"}' . PHP_EOL
        );

        (new EchoHandler())->handle(
            [new Misspelling('mispelling', 4, 10, ['misspelling', 'misspellings'], ['sentence' => 'two mispelling'])]
        );
    }
}
