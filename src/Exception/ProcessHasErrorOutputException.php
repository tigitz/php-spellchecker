<?php

declare(strict_types=1);

namespace PhpSpellcheck\Exception;

class ProcessHasErrorOutputException extends \RuntimeException implements ExceptionInterface
{
    public function __construct(string $errorOutput, string $parsedText, string $command)
    {
        $exceptionTemplateMessage = <<<'MSG'
            Process has generated the following output errors:

            %s

            With command: "%s"

            For text:
            "%s"
            MSG;

        parent::__construct(
            \Safe\sprintf(
                $exceptionTemplateMessage,
                $errorOutput,
                $command,
                $parsedText
            )
        );
    }
}
