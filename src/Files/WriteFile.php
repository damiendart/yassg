<?php

// Copyright (c) 2022 Damien Dart, <damiendart@pobox.com>.
// This file is distributed under the MIT licence. For more information,
// please refer to the accompanying "LICENCE" file.

declare(strict_types=1);

namespace Yassg\Files;

use Symfony\Component\Filesystem\Filesystem;

class WriteFile implements OutputFileInterface
{
    private string $contents;
    private string $relativePathname;

    public function __construct(
        string $contents,
        string $relativePathname,
    ) {
        $this->contents = $contents;
        $this->relativePathname = $relativePathname;
    }

    public function getRelativePathname(): string
    {
        return $this->relativePathname;
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
                    $this->getRelativePathname(),
                ],
            ),
            $this->contents,
        );
    }
}
