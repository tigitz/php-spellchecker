<?php

declare(strict_types=1);

namespace PhpSpellcheck\Exception;

use Symfony\Component\Process\Process;

class ProcessFailedException extends \RuntimeException implements ExceptionInterface
{
    /**
     * @var Process
     */
    private $process;

    public function __construct(
        Process $process,
        \Throwable $previous = null,
        string $failureReason = '',
        int $code = 0
    ) {
        $this->process = $process;

        $message = \Safe\sprintf(
            'Process with command "%s" has failed%s with exit code %d(%s)%s',
            $process->getCommandLine(),
            $process->isStarted() ? ' running' : '',
            $process->getExitCode(),
            $process->getExitCodeText(),
            $failureReason !== '' ? ' because "' . $failureReason . '"' : ''
        );

        parent::__construct(
            $message,
            $code,
            $previous
        );
    }

    public function getProcess(): Process
    {
        return $this->process;
    }
}
