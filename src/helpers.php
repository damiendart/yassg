<?php

/*
 * Copyright (c) 2022 Damien Dart, <damiendart@pobox.com>.
 * This file is distributed under the MIT licence. For more information,
 * please refer to the accompanying "LICENCE" file.
 */

declare(strict_types=1);

namespace Yassg;

use RuntimeException;

/**
 * Removes extraneous leading whitespace from multi-line strings.
 *
 * Single-line strings will simply have any leading whitespace removed.
 */
function dedent(string $input): string
{
    $lines = explode("\n", $input);
    $nonEmptyLines = array_filter(
        explode("\n", $input),
        fn (string $line): bool => '' !== $line,
    );

    if (count($nonEmptyLines) < 1) {
        return $input;
    }

    $shortestLeadingWhitespaceCount = min(
        array_map(
            function ($line): int {
                if (1 === preg_match('/^[ \t]+/', $line, $matches)) {
                    return strlen($matches[0]);
                }

                return 0;
            },
            $nonEmptyLines,
        ),
    );

    return join(
        "\n",
        array_map(
            fn ($line): string => substr($line, $shortestLeadingWhitespaceCount),
            $lines,
        ),
    );
}

/**
 * A wrapper for `\file_get_contents()` that always returns a string and
 * throws an exception when encountering an error instead of returning
 * `false`.
 *
 * @param resource $context
 * @param null|int<0, max> $length
 *
 * @see \file_get_contents() The core PHP function being wrapped
 *
 * @throws RuntimeException
 *
 * @codeCoverageIgnore
 */
function file_get_contents_safe(
    string $filename,
    bool $useIncludePath = false,
    $context = null,
    int $offset = 0,
    ?int $length = null,
): string {
    error_clear_last();
    set_error_handler(
        function (int $_, string $message): void {
            throw new RuntimeException($message);
        },
    );

    try {
        if (null !== $length) {
            /** @var string $contents */
            $contents = file_get_contents(
                $filename,
                $useIncludePath,
                $context,
                $offset,
                $length,
            );
        } else {
            /** @var string $contents */
            $contents = file_get_contents(
                $filename,
                $useIncludePath,
                $context,
                $offset,
            );
        }
    } finally {
        restore_error_handler();
    }

    return $contents;
}

/**
 * A wrapper for `\fopen()` that always returns a `resource` and throws
 * an exception when encountering an error instead of returning `false`.
 *
 * @param null|resource $context
 *
 * @see \fopen() The core PHP function being wrapped
 *
 * @return resource
 *
 * @throws RuntimeException
 *
 * @codeCoverageIgnore
 */
function fopen_safe(
    string $filename,
    string $mode,
    bool $useIncludePath = false,
    $context = null,
) {
    error_clear_last();
    set_error_handler(
        function (int $_, string $message): void {
            throw new RuntimeException($message);
        },
    );

    try {
        /** @var resource $resource */
        $resource = fopen($filename, $mode, $useIncludePath, $context);
    } finally {
        restore_error_handler();
    }

    return $resource;
}

/**
 * A wrapper for `\preg_replace()` that always returns a string or an
 * array of strings (depending on the _subject_ parameter) and throws an
 * exception when encountering an error instead of returning `null`.
 *
 * @param array<array-key, string>|string $pattern
 * @param array<array-key, float|int|string>|string $replacement
 * @param array<array-key, float|int|string>|string $subject
 *
 * @see \preg_replace() The core PHP function being wrapped
 *
 * @return ($subject is array ? array<string> : string)
 *
 * @throws RuntimeException
 *
 * @codeCoverageIgnore
 */
function preg_replace_safe(
    array|string $pattern,
    array|string $replacement,
    array|string $subject,
    int $limit = -1,
    int &$count = null,
): string|array {
    error_clear_last();

    $result = \preg_replace($pattern, $replacement, $subject, $limit, $count);

    if (PREG_NO_ERROR !== preg_last_error() || null === $result) {
        throw new RuntimeException(preg_last_error_msg());
    }

    return $result;
}
