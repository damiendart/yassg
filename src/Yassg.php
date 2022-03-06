<?php

// Copyright (c) 2022 Damien Dart, <damiendart@pobox.com>.
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
use Yassg\Events\PreSiteBuildEvent;
use Yassg\Exceptions\InvalidArgumentException;
use Yassg\Files\CopyFile;
use Yassg\Files\InputFile;
use Yassg\Files\InputFileCollection;
use Yassg\Files\Metadata\MetadataExtractorInterface;
use Yassg\Files\OutputFileInterface;
use Yassg\Processors\ProcessorResolver;

class Yassg
{
    private EventDispatcher $eventDispatcher;
    private Filesystem $filesystem;
    private Finder $finder;
    private MetadataExtractorInterface $metadataExtractor;
    private ProcessorResolver $processorResolver;

    public function __construct(
        EventDispatcher $eventDispatcher,
        Filesystem $filesystem,
        Finder $finder,
        MetadataExtractorInterface $metadataExtractor,
        ProcessorResolver $processorResolver,
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->filesystem = $filesystem;
        $this->finder = $finder;
        $this->metadataExtractor = $metadataExtractor;
        $this->processorResolver = $processorResolver;
    }

    /**
     * @psalm-suppress PossiblyNullArgument
     *
     * @throws InvalidArgumentException
     */
    public function build(Configuration $configuration): void
    {
        $finder = $this->finder
            ->files()
            ->in($configuration->getInputDirectory());
        $inputFiles = new InputFileCollection();

        $this->filesystem->mkdir($configuration->getOutputDirectory());

        $outputDirectory = $this->filesystem->readlink(
            $configuration->getOutputDirectory(),
            true,
        );

        foreach ($finder as $file) {
            $inputFile = new InputFile($file);

            $this->metadataExtractor->addMetadata($inputFile);
            $inputFiles->addInputFile($inputFile);
        }

        $this->eventDispatcher->dispatch(new PreSiteBuildEvent($inputFiles));

        foreach ($inputFiles as $inputFile) {
            $this->buildFile($inputFile, $outputDirectory);
        }
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
                    $inputFile->getOriginalAbsolutePathname(),
                    $processedFile->getRelativePathname(),
                    $baseOutputDirectory,
                ),
            );
        } else {
            $this->eventDispatcher->dispatch(
                new FileWrittenEvent(
                    $inputFile->getOriginalAbsolutePathname(),
                    $processedFile->getRelativePathname(),
                    $baseOutputDirectory,
                ),
            );
        }
    }
}
