<?php

// Copyright (C) 2021 Damien Dart, <damiendart@pobox.com>.
// This file is distributed under the MIT licence. For more information,
// please refer to the accompanying "LICENCE" file.

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
use Yassg\Files\InputFileInterface;
use Yassg\Files\MutatedFile;
use Yassg\Services\FrontMatterService;

class TwigProcessor implements ProcessorInterface
{
    private ChainLoader $chainLoader;
    private Configuration $configuration;
    private FilesystemLoader $filesystemLoader;
    private FrontMatterService $frontMatterService;

    public function __construct(
        ChainLoader $chainLoader,
        FilesystemLoader $filesystemLoader,
        Configuration $configuration,
        FrontMatterService $frontMatterService,
    ) {
        $this->chainLoader = $chainLoader;
        $this->configuration = $configuration;
        $this->filesystemLoader = $filesystemLoader;
        $this->frontMatterService = $frontMatterService;
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
        $this->filesystemLoader->addPath(
            $this->configuration->getInputDirectory(),
        );

        $this->chainLoader->addLoader(
            new ArrayLoader([
                $inputFile->getRelativePathname() => $this->frontMatterService->stripFrontMatter(
                    $inputFile->getContent(),
                ),
            ]),
        );
        $this->chainLoader->addLoader($this->filesystemLoader);

        $environment = new Environment(
            $this->chainLoader,
            ['strict_variables' => true],
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
