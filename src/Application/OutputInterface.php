<?php

/*
 * Copyright (c) 2022 Damien Dart, <damiendart@pobox.com>.
 * This file is distributed under the MIT licence. For more information,
 * please refer to the accompanying "LICENCE" file.
 */

declare(strict_types=1);

namespace Yassg\Application;

interface OutputInterface
{
    public const VERBOSITY_NORMAL = 2 ^ 0;
    public const VERBOSITY_VERBOSE = 2 ^ 1;

    public function isVerbose(): bool;

    public function setVerbosity(int $level): self;

    public function write(string $output): self;

    public function writeError(string $output): self;
}
