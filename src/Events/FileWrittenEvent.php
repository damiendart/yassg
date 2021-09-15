<?php

// Copyright (C) 2021 Damien Dart, <damiendart@pobox.com>.
// This file is distributed under the MIT licence. For more information,
// please refer to the accompanying "LICENCE" file.

declare(strict_types=1);

namespace Yassg\Events;

use Yassg\Files\OutputFileInterface;

class FileWrittenEvent extends Event
{
    private string $realFilepath;

    public function __construct(
        OutputFileInterface $outputFile,
        string $baseOutputDirectory,
    ) {
        $this->realFilepath = join(
            DIRECTORY_SEPARATOR,
            [
                $baseOutputDirectory,
                $outputFile->getRelativeFilepath(),
            ],
        );
    }

    public function getRealFilepath(): string
    {
        return $this->realFilepath;
    }
}
