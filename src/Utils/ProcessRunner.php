<?php

declare(strict_types=1);

namespace PhpSpellcheck\Utils;

use PhpSpellcheck\Exception\ProcessFailedException;
use Symfony\Component\Process\Exception\ExceptionInterface;
use Symfony\Component\Process\Process;

class ProcessRunner
{
    /**
     * @param float|int|null $timeout The timeout in seconds
     * @param array<string, string> $env
     */
    public static function run(Process $process, $timeout = null, callable $callback = null, array $env = []): Process
    {
        $process->setTimeout($timeout);

        try {
            $process->mustRun($callback, $env);
        } catch (ExceptionInterface $e) {
            throw new ProcessFailedException($process, $e);
        }

        return $process;
    }
}
