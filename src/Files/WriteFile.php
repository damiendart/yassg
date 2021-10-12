<?php

// Copyright (C) 2021 Damien Dart, <damiendart@pobox.com>.
// This file is distributed under the MIT licence. For more information,
// please refer to the accompanying "LICENCE" file.

declare(strict_types=1);

namespace Yassg\Files;

use Symfony\Component\Filesystem\Filesystem;

class WriteFile implements OutputFileInterface
{
    private string $contents;
    private string $relativeFilepath;

    public function __construct(
        string $contents,
        string $relativeFilepath,
    ) {
        $this->contents = $contents;
        $this->relativeFilepath = $relativeFilepath;
    }

    public function getRelativeFilepath(): string
    {
        return $this->relativeFilepath;
    }

    public function write(
        Filesystem $filesystem,
        string $baseOutputDirectory,
    ): void {
        $filesystem->dumpFile(
            join(
                DIRECTORY_SEPARATOR,
                [
                    $baseOutputDirectory,
                    $this->getRelativeFilepath(),
                ],
            ),
            $this->contents,
        );
    }
}
