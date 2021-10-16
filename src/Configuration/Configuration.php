<?php

// Copyright (C) 2021 Damien Dart, <damiendart@pobox.com>.
// This file is distributed under the MIT licence. For more information,
// please refer to the accompanying "LICENCE" file.

declare(strict_types=1);

namespace Yassg\Configuration;

class Configuration
{
    private string $inputDirectoryPath;
    private string $outputDirectoryPath;

    public function __construct(
        string $inputDirectoryPath,
        string $outputDirectoryPath,
    ) {
        $this->inputDirectoryPath = $inputDirectoryPath;
        $this->outputDirectoryPath = $outputDirectoryPath;
    }

    public function getInputDirectory(): string
    {
        return $this->inputDirectoryPath;
    }

    public function getOutputDirectory(): string
    {
        return $this->outputDirectoryPath;
    }
}
