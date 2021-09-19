<?php

// Copyright (C) 2021 Damien Dart, <damiendart@pobox.com>.
// This file is distributed under the MIT licence. For more information,
// please refer to the accompanying "LICENCE" file.

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

    public function getRelativeFilepath(): string
    {
        return $this->inputFile->getRelativeFilepath();
    }

    public function write(
        Filesystem $filesystem,
        string $baseOutputDirectory,
    ): void {
        $filesystem->copy(
            $this->inputFile->getRealFilepath(),
            join(
                DIRECTORY_SEPARATOR,
                [
                    $baseOutputDirectory,
                    $this->inputFile->getRelativeFilepath(),
                ],
            ),
            true,
        );
    }
}
