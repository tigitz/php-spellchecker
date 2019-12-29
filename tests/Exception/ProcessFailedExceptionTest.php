<?php

declare(strict_types=1);

use PhpSpellcheck\Exception\ProcessFailedException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Exception\ExceptionInterface;
use Symfony\Component\Process\Process;

class ProcessFailedExceptionTest extends TestCase
{
    public function testSymfonyRunningProcessFailedException(): void
    {
        $process = new Process(['non_existing_binaries']);

        try {
            $process->mustRun();
        } catch (ExceptionInterface $exception) {
            $processFailure = new ProcessFailedException($process, $exception);
            $this->assertStringContainsString(
                'Process with command "\'non_existing_binaries\'" has failed',
                $processFailure->getMessage()
            );
        }
    }

    public function testSymfonyBootingProcessFailedException(): void
    {
        $process = new Process(['echo test'], __DIR__ . '/notfound/');

        try {
            $process->mustRun();
        } catch (ExceptionInterface $exception) {
            $processFailure = new ProcessFailedException($process, $exception);
            $this->assertStringContainsString(
                'Process with command "\'echo test\'" has failed',
                $processFailure->getMessage()
            );

            return;
        }

        $this->markTestSkipped('Test is only relevant for symfony/process: ^4.0');
    }
}
