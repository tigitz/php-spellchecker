<?php

declare(strict_types=1);

namespace PhpSpellcheck\Exception;

class JsonException extends \JsonException implements ExceptionInterface
{
    public static function createFromPhpError(): self
    {
        return new self(json_last_error_msg(), json_last_error());
    }
}
