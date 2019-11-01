<?php

declare(strict_types=1);

use PhpSpellcheck\Exception\ProcessFailedException;
use PhpSpellcheck\Utils\ProcessRunner;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

class ProcessRunnerTest extends TestCase
{
    public function testRun(): void
    {
        $this->expectException(ProcessFailedException::class);
        ProcessRunner::run(new Process(['non_existing_binaries']));
    }
}
