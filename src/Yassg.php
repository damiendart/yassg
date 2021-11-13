<?php

// Copyright (C) 2021 Damien Dart, <damiendart@pobox.com>.
// This file is distributed under the MIT licence. For more information,
// please refer to the accompanying "LICENCE" file.

declare(strict_types=1);

namespace Yassg;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Yassg\Configuration\Configuration;
use Yassg\Events\EventDispatcher;
use Yassg\Events\FileCopiedEvent;
use Yassg\Events\FileWrittenEvent;
use Yassg\Exceptions\InvalidConfigurationException;
use Yassg\Files\CopyFile;
use Yassg\Files\InputFile;
use Yassg\Files\OutputFileInterface;
use Yassg\Processors\ProcessorResolver;

class Yassg
{
    private EventDispatcher $eventDispatcher;
    private Filesystem $filesystem;
    private Finder $finder;
    private ProcessorResolver $processorResolver;

    public function __construct(
        EventDispatcher $eventDispatcher,
        Filesystem $filesystem,
        Finder $finder,
        ProcessorResolver $processorResolver,
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->filesystem = $filesystem;
        $this->finder = $finder;
        $this->processorResolver = $processorResolver;
    }

    /**
     * @throws InvalidConfigurationException
     */
    public function build(Configuration $configuration): void
    {
        $this
            ->validateInputDirectory($configuration->getInputDirectory())
            ->buildSite(
                $configuration->getInputDirectory(),
                $configuration->getOutputDirectory(),
            );
    }

    private function buildFile(
        InputFile $inputFile,
        string $baseOutputDirectory,
    ): void {
        $processedFile = $inputFile;

        while (false === $processedFile instanceof OutputFileInterface) {
            $processor = $this->processorResolver->getApplicableProcessor(
                $processedFile,
            );

            $processedFile = $processor->process($processedFile);
        }

        $processedFile->write($this->filesystem, $baseOutputDirectory);

        if ($processedFile instanceof CopyFile) {
            $this->eventDispatcher->dispatch(
                new FileCopiedEvent(
                    $inputFile,
                    $processedFile,
                    $baseOutputDirectory,
                ),
            );
        } else {
            $this->eventDispatcher->dispatch(
                new FileWrittenEvent(
                    $inputFile,
                    $processedFile,
                    $baseOutputDirectory,
                ),
            );
        }
    }

    /**
     * @psalm-suppress PossiblyNullArgument
     */
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
            true,
        );

        /** @var SplFileInfo $file */
        foreach ($finder as $file) {
            $this->buildFile(new InputFile($file), $outputDirectory);
        }
    }

    /**
     * @throws InvalidConfigurationException
     */
    private function validateInputDirectory(string $inputDirectory): self
    {
        if (false === $this->filesystem->exists($inputDirectory)) {
            throw new InvalidConfigurationException(
                "The input directory (\"{$inputDirectory}\") does not exist.",
            );
        }

        return $this;
    }
}
