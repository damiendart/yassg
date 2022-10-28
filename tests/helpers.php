<?php

/*
 * Copyright (c) 2022 Damien Dart, <damiendart@pobox.com>.
 * This file is distributed under the MIT licence. For more information,
 * please refer to the accompanying "LICENCE" file.
 */

declare(strict_types=1);

namespace Yassg\Tests;

use RuntimeException;

/**
 * A wrapper for `\stream_get_contents()` that always returns a string
 * and throws an exception when encountering an error instead of
 * returning `false`.
 *
 * @param resource $stream
 *
 * @see \stream_get_contents() The core PHP function being wrapped
 *
 * @throws RuntimeException
 */
function stream_get_contents_safe(
    $stream,
    ?int $length = null,
    int $offset = -1,
): string {
    error_clear_last();
    set_error_handler(
        function (int $_, string $message): void {
            throw new RuntimeException($message);
        },
    );

    try {
        if (-1 !== $offset && null !== $length) {
            /** @var string $contents */
            $contents = stream_get_contents($stream, $length, $offset);
        } elseif (null !== $length) {
            /** @var string $contents */
            $contents = stream_get_contents($stream, $length);
        } else {
            /** @var string $contents */
            $contents = stream_get_contents($stream);
        }
    } finally {
        restore_error_handler();
    }

    return $contents;
}
