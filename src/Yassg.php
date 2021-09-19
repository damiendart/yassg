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
    private EventDispatcher $eventDispatcher;
    private Filesystem $filesystem;
    private Finder $finder;

    public function __construct(
        EventDispatcher $eventDispatcher,
        Filesystem $filesystem,
        Finder $finder,
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->filesystem = $filesystem;
        $this->finder = $finder;
    }

    /**
     * @param $processors ProcessorInterface[]
     *
     * @throws InvalidInputDirectoryException
     */
    public function build(
        string $inputDirectory,
        string $outputDirectory,
        array $processors = [],
    ): void {
        $this
            ->validateInputDirectory($inputDirectory)
            ->buildSite($inputDirectory, $outputDirectory, $processors);
    }

    private function buildFile(
        InputFile $inputFile,
        ProcessorInterface $processor,
        string $baseOutputDirectory,
    ): void {
        $outputFile = $processor->process($inputFile);

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

    /**
     * @psalm-suppress PossiblyNullArgument
     *
     * @param $processors ProcessorInterface[]
     */
    private function buildSite(
        string $inputDirectory,
        string $outputDirectory,
        array $processors,
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
            $file = new InputFile($file);

            array_map(
                function (ProcessorInterface $processor) use ($file, $outputDirectory): void {
                    $this->buildFile($file, $processor, $outputDirectory);
                },
                $this->filterProcessors($processors, $file),
            );
        }
    }

    /**
     * @param $processors ProcessorInterface[]
     *
     * @return ProcessorInterface[]
     */
    private function filterProcessors(
        array $processors,
        InputFile $inputFile,
    ): array {
        return array_filter(
            $processors,
            function (ProcessorInterface $processor) use ($inputFile): bool {
                return $processor->canProcess($inputFile);
            },
        );
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
