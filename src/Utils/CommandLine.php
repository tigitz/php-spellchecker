<?php

declare(strict_types=1);

namespace PhpSpellcheck\Utils;

use PhpSpellcheck\Exception\InvalidArgumentException;

class CommandLine
{
    /**
     * @var array<string>
     */
    private $commandArgs;

    /**
     * @param array<string>|string $command
     */
    public function __construct($command)
    {
        if (\is_array($command)) {
            $this->commandArgs = $command;
        } elseif (\is_string($command)) {
            $this->commandArgs = [$command];
        } else {
            throw new InvalidArgumentException(
                \Safe\sprintf(
                    'Command should be an "array<string>" or a "string", "%s" given',
                    \is_object($command) ? \get_class($command) : \gettype($command)
                )
            );
        }
    }

    public function addArg(string $arg): self
    {
        $args = $this->commandArgs;
        $args[] = $arg;

        return new self($args);
    }

    /**
     * @param iterable<string> $argsToAdd
     */
    public function addArgs(iterable $argsToAdd): self
    {
        $args = $this->commandArgs;

        foreach ($argsToAdd as $arg) {
            $args[] = $arg;
        }

        return new self($args);
    }

    /**
     * @return array<string>
     */
    public function getArgs(): array
    {
        return $this->commandArgs;
    }

    public function asString(): string
    {
        return implode(' ', array_map([$this, 'escapeArgument'], $this->commandArgs));
    }

    /**
     * Escapes a string to be used as a shell argument.
     */
    private function escapeArgument(string $argument): string
    {
        if ('\\' !== \DIRECTORY_SEPARATOR) {
            return "'" . str_replace("'", "'\\''", $argument) . "'";
        }
        if ('' === $argument) {
            return '""';
        }
        if (false !== strpos($argument, "\0")) {
            $argument = str_replace("\0", '?', $argument);
        }
        if (\Safe\preg_match('/[\/()%!^"<>&|\s]/', $argument) !== 0) {
            return $argument;
        }
        $argument = \Safe\preg_replace('/(\\\\+)$/', '$1$1', $argument);

        return '"' . str_replace(['"', '^', '%', '!', "\n"], ['""', '"^^"', '"^%"', '"^!"', '!LF!'], $argument) . '"';
    }
}
