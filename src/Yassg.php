<?php

/*
 * Copyright (c) 2022 Damien Dart, <damiendart@pobox.com>.
 * This file is distributed under the MIT licence. For more information,
 * please refer to the accompanying "LICENCE" file.
 */

declare(strict_types=1);

namespace Yassg;

use Symfony\Component\Filesystem\Filesystem;
use Yassg\Application\InvalidArgumentException;
use Yassg\Configuration\Configuration;
use Yassg\Events\EventDispatcher;
use Yassg\Events\FileCopiedEvent;
use Yassg\Events\FileWrittenEvent;
use Yassg\Events\PreSiteBuildEvent;
use Yassg\Files\CopyFile;
use Yassg\Files\InputFile;
use Yassg\Files\InputFileCollection;
use Yassg\Files\InputFileInterface;
use Yassg\Files\Metadata\MetadataExtractorInterface;
use Yassg\Files\OutputFileInterface;
use Yassg\Files\Processors\ProcessorResolver;

class Yassg
{
    /**
     * @psalm-api
     */
    public function __construct(
        readonly private Configuration $configuration,
        readonly private EventDispatcher $eventDispatcher,
        readonly private Filesystem $filesystem,
        readonly private Finder $finder,
        readonly private MetadataExtractorInterface $metadataExtractor,
        readonly private ProcessorResolver $processorResolver,
    ) {}

    /**
     * @psalm-suppress PossiblyNullArgument
     *
     * @throws BuildException
     * @throws InvalidArgumentException
     */
    public function build(Configuration $configuration): void
    {
        $finder = $this->finder
            ->files()
            ->in($configuration->getInputDirectory());
        $inputFiles = new InputFileCollection();

        $this->filesystem->mkdir($configuration->getOutputDirectory());

        /** @var string $outputDirectory */
        $outputDirectory = $this->filesystem->readlink(
            $configuration->getOutputDirectory(),
            true,
        );

        foreach ($finder as $file) {
            try {
                $inputFile = new InputFile(
                    $file->getRealPath(),
                    $file->getRelativePathname(),
                );

                $this->metadataExtractor->addMetadata($inputFile);
            } catch (\Throwable $throwable) {
                throw new BuildException(
                    sprintf(
                        'Unable to pre-process "%s"',
                        $file->getRealPath(),
                    ),
                    \is_int($throwable->getCode()) ? $throwable->getCode() : 0,
                    $throwable,
                );
            }

            $inputFiles->addInputFile($inputFile);
        }

        $this->eventDispatcher->dispatch(new PreSiteBuildEvent($inputFiles));

        /** @var InputFileInterface $inputFile */
        foreach ($inputFiles as $inputFile) {
            try {
                $inputFile->setMetadata(
                    array_merge(
                        $this->configuration->getMetadata(),
                        $inputFile->getMetadata(),
                    ),
                );
                $this->buildFile($inputFile, $outputDirectory);
            } catch (\Throwable $throwable) {
                throw new BuildException(
                    sprintf(
                        'Unable to process "%s"',
                        $inputFile->getOriginalAbsolutePathname(),
                    ),
                    \is_int($throwable->getCode()) ? $throwable->getCode() : 0,
                    $throwable,
                );
            }
        }
    }

    private function buildFile(
        InputFileInterface $inputFile,
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
