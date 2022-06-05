<?php

/*
 * Copyright (c) 2022 Damien Dart, <damiendart@pobox.com>.
 * This file is distributed under the MIT licence. For more information,
 * please refer to the accompanying "LICENCE" file.
 */

declare(strict_types=1);

namespace Yassg\Files;

use Symfony\Component\Filesystem\Filesystem;

class CopyFile implements OutputFileInterface
{
    private InputFile $inputFile;

    public function __construct(InputFile $inputFile)
    {
        $this->inputFile = $inputFile;
    }

    public function getRelativePathname(): string
    {
        return $this->inputFile->getRelativePathname();
    }

    public function write(
        Filesystem $filesystem,
        string $baseOutputDirectory,
    ): void {
        $filesystem->copy(
            $this->inputFile->getOriginalAbsolutePathname(),
            join(
                DIRECTORY_SEPARATOR,
                [
                    $baseOutputDirectory,
                    $this->inputFile->getRelativePathname(),
                ],
            ),
            true,
        );
    }
}
