<?php

declare(strict_types=1);

namespace PhpSpellcheck\Tests\Exception;

use PhpSpellcheck\Exception\ProcessFailedException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Exception\ExceptionInterface;
use Symfony\Component\Process\Process;

class ProcessFailedExceptionTest extends TestCase
{
    public function testSymfonyRunningProcessFailedException()
    {
        $process = new Process('non_existing_binaries');
        try {
            $process->mustRun();
        } catch (ExceptionInterface $exception) {
            $processFailure = new ProcessFailedException($process, $exception);
            $this->assertSame(
                'Process with command "non_existing_binaries" has failed running with exit code 127(Command not found)',
                $processFailure->getMessage()
            );
        }
    }

    public function testSymfonyBootingProcessFailedException()
    {
        $process = new Process('echo test', __DIR__ . '/notfound/');
        try {
            $process->mustRun();
        } catch (ExceptionInterface $exception) {
            $processFailure = new ProcessFailedException($process, $exception);
            $this->assertSame(
                'Process with command "echo test" has failed with exit code 0()',
                $processFailure->getMessage()
            );

            return;
        }

        $this->markTestSkipped('Test is only relevant for symfony/process: ^4.0');
    }
}
