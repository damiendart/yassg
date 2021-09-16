<?php

// Copyright (C) 2021 Damien Dart, <damiendart@pobox.com>.
// This file is distributed under the MIT licence. For more information,
// please refer to the accompanying "LICENCE" file.

declare(strict_types=1);

namespace Yassg;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Yassg\Events\EventDispatcher;
use Yassg\Events\FileCopiedEvent;
use Yassg\Exceptions\InvalidInputDirectoryException;
use Yassg\Files\CopyFile;
use Yassg\Files\InputFile;
use Yassg\Processors\ProcessorInterface;

class Yassg
{
    private Config $config;
    private EventDispatcher $eventDispatcher;
    private Filesystem $filesystem;
    private Finder $finder;

    public function __construct(
        Config $config,
        EventDispatcher $eventDispatcher,
        Filesystem $filesystem,
        Finder $finder,
    ) {
        $this->config = $config;
        $this->eventDispatcher = $eventDispatcher;
        $this->filesystem = $filesystem;
        $this->finder = $finder;
    }

    /**
     * @throws InvalidInputDirectoryException
     */
    public function build(
        string $inputDirectory,
        string $outputDirectory,
    ): void {
        $this
            ->validateInputDirectory($inputDirectory)
            ->buildSite($inputDirectory, $outputDirectory);
    }

    private function buildFile(
        InputFile $inputFile,
        string $baseOutputDirectory
    ): void {
        $outputFile = $this->getProcessor($inputFile)->process($inputFile);

        $outputFile->write($this->filesystem, $baseOutputDirectory);

        if ($outputFile instanceof CopyFile) {
            $this->eventDispatcher->dispatch(
                new FileCopiedEvent(
                    $inputFile,
                    $outputFile,
                    $baseOutputDirectory,
                )
            );
        }
    }

    private function buildSite(
        string $inputDirectory,
        string $outputDirectory,
    ): void {
        $finder = $this->finder
            ->files()
            ->in($inputDirectory)
            ->ignoreDotFiles(true);

        $this->filesystem->mkdir($outputDirectory);

        $outputDirectory = $this->filesystem->readlink(
            $outputDirectory,
            true
        );

        foreach ($finder as $file) {
            $this->buildFile(new InputFile($file), $outputDirectory);
        }
    }

    private function getProcessor($inputFile): ?ProcessorInterface
    {
        foreach ($this->config->getProcessors() as $processor) {
            if ($processor->canProcess($inputFile)) {
                return $processor;
            }
        }

        return null;
    }

    /**
     * @throws InvalidInputDirectoryException
     */
    private function validateInputDirectory(string $inputDirectory): self
    {
        if (false === $this->filesystem->exists($inputDirectory)) {
            throw new InvalidInputDirectoryException(
                "The input directory (\"{$inputDirectory}\") does not exist.",
            );
        }

        return $this;
    }
}
