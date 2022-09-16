<?php

/*
 * Copyright (c) 2022 Damien Dart, <damiendart@pobox.com>.
 * This file is distributed under the MIT licence. For more information,
 * please refer to the accompanying "LICENCE" file.
 */

declare(strict_types=1);

namespace Yassg\Configuration;

use Yassg\Metadata\MetadataTrait;
use Yassg\Plugins\PluginInterface;

class Configuration
{
    use MetadataTrait;

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

        $this->validateInputDirectory();
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

    private function validateInputDirectory(): void
    {
        if (false === is_dir($this->inputDirectory)) {
            throw new InvalidInputDirectoryException(
                "The input directory (\"{$this->inputDirectory}\") does not exist.",
            );
        }
    }
}
