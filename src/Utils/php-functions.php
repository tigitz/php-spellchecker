<?php

declare(strict_types=1);

namespace PhpSpellcheck;

use PhpSpellcheck\Exception\FilesystemException;
use PhpSpellcheck\Exception\JsonException;
use PhpSpellcheck\Exception\PcreException;
use PhpSpellcheck\Exception\PspellException;
use PSpell\Config;
use PSpell\Dictionary;

/**
 * @template TFlags as int
 *
 * @param mixed $matches
 * @param TFlags $flags
 *
 * @param-out (
 *          TFlags is 1
 *          ? array<list<string>>
 *          : (TFlags is 2
 *              ? list<array<string>>
 *              : (TFlags is 256|257
 *                  ? array<list<array{string, int}>>
 *                  : (TFlags is 258
 *                      ? list<array<array{string, int}>>
 *                      : (TFlags is 512|513
 *                          ? array<list<?string>>
 *                          : (TFlags is 514
 *                              ? list<array<?string>>
 *                              : (TFlags is 770
 *                                  ? list<array<array{?string, int}>>
 *                                  : (TFlags is 0 ? array<list<string>> : array<mixed>)
 *                              )
 *                          )
 *                      )
 *                  )
 *              )
 *          )
 *        ) $matches
 */
function preg_match_all(string $pattern, string $subject, &$matches = [], int $flags = 1, int $offset = 0): int
{
    error_clear_last();
    $safeResult = \preg_match_all($pattern, $subject, $matches, $flags, $offset);
    if ($safeResult === false) {
        throw PcreException::createFromPhpError();
    }

    return $safeResult;
}

/**
 * @param string|string[] $pattern
 * @param array<float|int|string>|string $replacement
 * @param array<float|int|string>|string $subject
 *
 * @param-out 0|positive-int $count
 *
 * @return ($subject is array ? list<string> : string)
 */
function preg_replace(array|string $pattern, array|string $replacement, array|string $subject, int $limit = -1, ?int &$count = null): array|string
{
    error_clear_last();
    $result = \preg_replace($pattern, $replacement, $subject, $limit, $count);
    if (preg_last_error() !== PREG_NO_ERROR || $result === null) {
        throw PcreException::createFromPhpError();
    }

    return $result;
}

/**
 * @template TFlags as int-mask<0, 256, 512>
 *
 * @param mixed $matches
 * @param TFlags $flags
 *
 * @param-out (
 *             TFlags is 256
 *             ? array<array-key, array{string, 0|positive-int}|array{'', -1}>
 *             : (TFlags is 512
 *                 ? array<array-key, string|null>
 *                 : (TFlags is 768
 *                     ? array<array-key, array{string, 0|positive-int}|array{null, -1}>
 *                     : array<array-key, string>
 *                     )
 *                 )
 *             ) $matches
 *
 * @return 0|1
 */
function preg_match(string $pattern, string $subject, &$matches = [], int $flags = 0, int $offset = 0)
{
    error_clear_last();
    $safeResult = \preg_match($pattern, $subject, $matches, $flags, $offset);
    if ($safeResult === false) {
        throw PcreException::createFromPhpError();
    }

    return $safeResult;
}

/**
 * @param ?resource $context
 * @param int<0, max>|null $length
 */
function file_get_contents(string $filename, bool $use_include_path = false, $context = null, int $offset = 0, ?int $length = null): string
{
    error_clear_last();
    if ($length !== null) {
        $safeResult = \file_get_contents($filename, $use_include_path, $context, $offset, $length);
    } elseif ($offset !== 0) {
        $safeResult = \file_get_contents($filename, $use_include_path, $context, $offset);
    } elseif ($context !== null) {
        $safeResult = \file_get_contents($filename, $use_include_path, $context);
    } else {
        $safeResult = \file_get_contents($filename, $use_include_path);
    }
    if ($safeResult === false) {
        throw FilesystemException::createFromPhpError();
    }

    return $safeResult;
}

/**
 * @param ?resource $context
 */
function file_put_contents(string $filename, mixed $data, int $flags = 0, $context = null): int
{
    error_clear_last();
    if ($context !== null) {
        $safeResult = \file_put_contents($filename, $data, $flags, $context);
    } else {
        $safeResult = \file_put_contents($filename, $data, $flags);
    }
    if ($safeResult === false) {
        throw FilesystemException::createFromPhpError();
    }

    return $safeResult;
}

function realpath(string $path): string
{
    error_clear_last();
    $safeResult = \realpath($path);
    if ($safeResult === false) {
        throw FilesystemException::createFromPhpError();
    }

    return $safeResult;
}

/**
 * @param int<1, max> $flags
 */
function json_encode(mixed $value, int $flags = 0, int $depth = 512): string
{
    error_clear_last();
    $safeResult = \json_encode($value, $flags, $depth);
    if ($safeResult === false) {
        throw JsonException::createFromPhpError();
    }

    return $safeResult;
}

function json_decode(string $json, bool $assoc = false, int $depth = 512, int $flags = 0): mixed
{
    $data = \json_decode($json, $assoc, $depth, $flags);
    if (JSON_ERROR_NONE !== json_last_error()) {
        throw JsonException::createFromPhpError();
    }

    return $data;
}

function pspell_new_config(Config $config): Dictionary
{
    error_clear_last();
    $result = \pspell_new_config($config);
    if ($result === false) {
        throw PspellException::createFromPhpError();
    }

    return $result;
}
