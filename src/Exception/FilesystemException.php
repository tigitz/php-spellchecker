<?php

declare(strict_types=1);

namespace PhpSpellcheck\Exception;

class FilesystemException extends \ErrorException implements ExceptionInterface
{
    public static function createFromPhpError(): self
    {
        $error = error_get_last();

        return new self($error['message'] ?? 'An error occured', 0, $error['type'] ?? 1);
    }
}
