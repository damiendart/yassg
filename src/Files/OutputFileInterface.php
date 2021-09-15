<?php

// Copyright (C) 2021 Damien Dart, <damiendart@pobox.com>.
// This file is distributed under the MIT licence. For more information,
// please refer to the accompanying "LICENCE" file.

declare(strict_types=1);

namespace Yassg\Files;

use Symfony\Component\Filesystem\Filesystem;

interface OutputFileInterface
{
    public function getRelativeFilepath(): string;

    public function write(
        Filesystem $filesystem,
        string $baseOutputDirectory
    ): void;
}
