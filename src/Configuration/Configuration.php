<?php

// Copyright (C) 2021 Damien Dart, <damiendart@pobox.com>.
// This file is distributed under the MIT licence. For more information,
// please refer to the accompanying "LICENCE" file.

declare(strict_types=1);

namespace Yassg\Configuration;

use Yassg\Traits\HasMetadata;

class Configuration
{
    use HasMetadata;

    private string $inputDirectory;
    private string $outputDirectory;

    public function __construct(
        string $inputDirectory,
        string $outputDirectory,
    ) {
        $this->inputDirectory = $inputDirectory;
        $this->outputDirectory = $outputDirectory;
    }

    public function getInputDirectory(): string
    {
        return $this->inputDirectory;
    }

    public function getOutputDirectory(): string
    {
        return $this->outputDirectory;
    }
}
