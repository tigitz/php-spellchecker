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
     */
    public static function run(Process $process, $timeout = null, callable $callback = null, array $env = []): Process
    {
        if (method_exists($process, 'inheritEnvironmentVariables')) {
            // Symfony 3.2+
            $process->inheritEnvironmentVariables(true);
        } else {
            // Symfony < 3.2
            $process->setEnv(['LANG' => getenv('LANG')]);
        }
        $process->setTimeout($timeout);

        try {
            $process->mustRun($callback, $env);
        } catch (ExceptionInterface $e) {
            throw new ProcessFailedException($process, $e);
        }

        return $process;
    }
}
