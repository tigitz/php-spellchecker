<?php

declare(strict_types=1);

use PhpSpellcheck\Exception\InvalidArgumentException;
use PhpSpellcheck\Utils\CommandLine;
use PHPUnit\Framework\TestCase;

class CommandLineTest extends TestCase
{
    public function testCreate(): void
    {
        $this->assertInstanceOf(CommandLine::class, new CommandLine('ls'));
        $this->assertInstanceOf(CommandLine::class, new CommandLine(['ls']));
    }

    public function testCreateWithInvalidArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new CommandLine(4);
    }

    public function testAsString(): void
    {
        $this->assertSame("'ls' '-lsa'", (new CommandLine(['ls', '-lsa']))->asString());
    }
}
