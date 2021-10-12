<?php

// Copyright (C) 2021 Damien Dart, <damiendart@pobox.com>.
// This file is distributed under the MIT licence. For more information,
// please refer to the accompanying "LICENCE" file.

declare(strict_types=1);

namespace Yassg;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Yassg\Configuration\Configuration;
use Yassg\Events\EventDispatcher;
use Yassg\Events\FileCopiedEvent;
use Yassg\Events\FileWrittenEvent;
use Yassg\Exceptions\InvalidConfigurationException;
use Yassg\Files\CopyFile;
use Yassg\Files\InputFile;
use Yassg\Processors\DefaultProcessor;
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
     * @throws InvalidConfigurationException
     */
    public function build(Configuration $configuration): void
    {
        $this
            ->validateInputDirectory($configuration->getInputDirectory())
            ->buildSite(
                $configuration->getInputDirectory(),
                $configuration->getOutputDirectory(),
                $configuration->getProcessors(),
            );
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
                ),
            );
        } else {
            $this->eventDispatcher->dispatch(
                new FileWrittenEvent(
                    $inputFile,
                    $outputFile,
                    $baseOutputDirectory,
                ),
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
            true,
        );

        foreach ($finder as $file) {
            $file = new InputFile($file);

            $this->buildFile(
                $file,
                $this->getApplicableProcessor($processors, $file),
                $outputDirectory,
            );
        }
    }

    /**
     * @param $processors ProcessorInterface[]
     */
    private function getApplicableProcessor(
        array $processors,
        InputFile $inputFile,
    ): ProcessorInterface {
        foreach ($processors as $processor) {
            if ($processor->canProcess($inputFile)) {
                return $processor;
            }
        }

        return new DefaultProcessor();
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
