<?php

// Copyright (c) 2022 Damien Dart, <damiendart@pobox.com>.
// This file is distributed under the MIT licence. For more information,
// please refer to the accompanying "LICENCE" file.

declare(strict_types=1);

namespace Yassg\Processors;

use League\CommonMark\MarkdownConverterInterface;
use RuntimeException;
use Yassg\Configuration\Configuration;
use Yassg\Files\InputFileInterface;
use Yassg\Files\MutatedFile;

class MarkdownProcessor implements ProcessorInterface
{
    private Configuration $configuration;
    private MarkdownConverterInterface $converter;

    public function __construct(
        MarkdownConverterInterface $converter,
        Configuration $configuration,
    ) {
        $this->converter = $converter;
        $this->configuration = $configuration;
    }

    public function canProcess(InputFileInterface $file): bool
    {
        return str_ends_with($file->getRelativePathname(), 'md');
    }

    public function process(InputFileInterface $inputFile): MutatedFile
    {
        $renderedMarkdown =
            $this->converter->convertToHtml(
                $inputFile->getContent(),
            )->getContent();

        if (array_key_exists('twigTemplate', $inputFile->getMetadata())) {
            /** @var string $twigTemplate */
            $twigTemplate = $inputFile->getMetadata()['twigTemplate'];

            return new MutatedFile(
                $this->getTwigTemplateContent($twigTemplate),
                array_merge(
                    $this->configuration->getMetadata(),
                    $inputFile->getMetadata(),
                    ['renderedMarkdown' => $renderedMarkdown],
                ),
                $inputFile->getOriginalInputFile(),
                $this->processPathname($inputFile->getRelativePathname()) . '.twig',
            );
        }

        return new MutatedFile(
            $renderedMarkdown,
            $inputFile->getMetadata(),
            $inputFile->getOriginalInputFile(),
            $this->processPathname($inputFile->getRelativePathname()),
        );
    }

    private function getTwigTemplateContent(string $relativePathname): string
    {
        $twigTemplate = file_get_contents(
            join(
                DIRECTORY_SEPARATOR,
                [
                    $this->configuration->getInputDirectory(),
                    $relativePathname,
                ],
            ),
        );

        if (false === $twigTemplate) {
            /**
             * @psalm-suppress PossiblyNullArrayAccess
             *
             * @var string $errorMessage
             */
            $errorMessage = error_get_last()['message'];

            throw new RuntimeException($errorMessage);
        }

        return $twigTemplate;
    }

    private function processPathname(string $pathname): string
    {
        $extension = pathinfo($pathname, PATHINFO_EXTENSION);

        return preg_replace("/{$extension}$/i", 'html', $pathname);
    }
}
