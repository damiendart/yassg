<?php

// Copyright (c) 2022 Damien Dart, <damiendart@pobox.com>.
// This file is distributed under the MIT licence. For more information,
// please refer to the accompanying "LICENCE" file.

declare(strict_types=1);

namespace Yassg\Configuration;

use Yassg\Plugins\PluginInterface;
use Yassg\Traits\HasMetadata;

class Configuration
{
    use HasMetadata;

    private string $inputDirectory;
    private string $outputDirectory;

    /** @var PluginInterface[] */
    private array $plugins = [];

    public function __construct(
        string $inputDirectory,
        string $outputDirectory,
    ) {
        $this->inputDirectory = $inputDirectory;
        $this->outputDirectory = $outputDirectory;
    }

    /** @return PluginInterface[] */
    public function getPlugins(): array
    {
        return $this->plugins;
    }

    public function getInputDirectory(): string
    {
        return $this->inputDirectory;
    }

    public function getOutputDirectory(): string
    {
        return $this->outputDirectory;
    }

    public function addPlugin(PluginInterface $plugin): self
    {
        $this->plugins[] = $plugin;

        return $this;
    }
}
