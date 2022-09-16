<?php

/*
 * Copyright (c) 2022 Damien Dart, <damiendart@pobox.com>.
 * This file is distributed under the MIT licence. For more information,
 * please refer to the accompanying "LICENCE" file.
 */

declare(strict_types=1);

namespace Yassg;

use RuntimeException;
use Throwable;

/**
 * @codeCoverageIgnore
 */
class BuildException extends RuntimeException
{
    public function __construct(
        string $message,
        int $code,
        Throwable $previous,
    ) {
        parent::__construct($message, $code, $previous);
    }
}
