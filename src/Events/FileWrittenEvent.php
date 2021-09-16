<?php

// Copyright (C) 2021 Damien Dart, <damiendart@pobox.com>.
// This file is distributed under the MIT licence. For more information,
// please refer to the accompanying "LICENCE" file.

declare(strict_types=1);

namespace Yassg\Events;

use Yassg\Files\InputFile;
use Yassg\Files\OutputFileInterface;

class FileWrittenEvent extends Event
{
    private InputFile $inputFile;
    private string $realOutputFilepath;

    public function __construct(
        InputFile $inputFile,
        OutputFileInterface $outputFile,
        string $baseOutputDirectory,
    ) {
        $this->inputFile = $inputFile;
        $this->realOutputFilepath = join(
            DIRECTORY_SEPARATOR,
            [
                $baseOutputDirectory,
                $outputFile->getRelativeFilepath(),
            ],
        );
    }

    public function getRealInputFilepath(): string
    {
        return $this->inputFile->getRealFilepath();
    }

    public function getRealOutputFilepath(): string
    {
        return $this->realOutputFilepath;
    }
}
