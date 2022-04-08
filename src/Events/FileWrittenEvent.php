<?php

// Copyright (c) 2022 Damien Dart, <damiendart@pobox.com>.
// This file is distributed under the MIT licence. For more information,
// please refer to the accompanying "LICENCE" file.

declare(strict_types=1);

namespace Yassg\Events;

class FileWrittenEvent extends Event implements FileEventInterface
{
    private string $inputAbsolutePathname;
    private string $outputAbsolutePathname;

    public function __construct(
        string $inputAbsolutePathname,
        string $outputRelativePathname,
        string $baseOutputDirectory,
    ) {
        $this->inputAbsolutePathname = $inputAbsolutePathname;
        $this->outputAbsolutePathname = join(
            DIRECTORY_SEPARATOR,
            [$baseOutputDirectory, $outputRelativePathname],
        );
    }

    public function getInputAbsolutePathname(): string
    {
        return $this->inputAbsolutePathname;
    }

    public function getOutputAbsolutePathname(): string
    {
        return $this->outputAbsolutePathname;
    }
}
