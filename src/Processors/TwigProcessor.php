<?php

/*
 * Copyright (c) 2022 Damien Dart, <damiendart@pobox.com>.
 * This file is distributed under the MIT licence. For more information,
 * please refer to the accompanying "LICENCE" file.
 */

declare(strict_types=1);

namespace Yassg\Processors;

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\ArrayLoader;
use Twig\Loader\ChainLoader;
use Twig\Loader\FilesystemLoader;
use Yassg\Configuration\Configuration;
use Yassg\Events\EventDispatcher;
use Yassg\Events\TwigEnvironmentCreatedEvent;
use Yassg\Files\InputFileInterface;
use Yassg\Files\MutatedFile;

class TwigProcessor implements ProcessorInterface
{
    private EventDispatcher $eventDispatcher;
    private Configuration $configuration;
    private FilesystemLoader $filesystemLoader;

    public function __construct(
        FilesystemLoader $filesystemLoader,
        Configuration $configuration,
        EventDispatcher $eventDispatcher,
    ) {
        $this->configuration = $configuration;
        $this->eventDispatcher = $eventDispatcher;
        $this->filesystemLoader = $filesystemLoader;
    }

    public function canProcess(InputFileInterface $file): bool
    {
        return str_ends_with($file->getRelativePathname(), 'twig');
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function process(InputFileInterface $inputFile): MutatedFile
    {
        $chainLoader = new ChainLoader();

        $this->filesystemLoader->addPath(
            $this->configuration->getInputDirectory(),
        );

        // If the input file content has been modified internally by
        // yassg (during the metadata extraction process, by another
        // processor, etc), the "ArrayLoader" ensures the modified
        // content is used by Twig instead of the original file content.
        $chainLoader->addLoader(
            new ArrayLoader([
                $inputFile->getRelativePathname() => $inputFile->getContent(),
            ]),
        );
        $chainLoader->addLoader($this->filesystemLoader);

        $environment = new Environment(
            $chainLoader,
            ['strict_variables' => true],
        );

        $this->eventDispatcher->dispatch(
            new TwigEnvironmentCreatedEvent($environment),
        );

        return new MutatedFile(
            $environment->render(
                $inputFile->getRelativePathname(),
                $inputFile->getMetadata(),
            ),
            $inputFile->getMetadata(),
            $inputFile->getOriginalInputFile(),
            $this->processPathname($inputFile->getRelativePathname()),
        );
    }

    private function processPathname(string $pathname): string
    {
        return preg_replace('/.twig$/i', '', $pathname);
    }
}
