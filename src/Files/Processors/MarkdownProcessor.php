<?php

/*
 * Copyright (c) 2022 Damien Dart, <damiendart@pobox.com>.
 * This file is distributed under the MIT licence. For more information,
 * please refer to the accompanying "LICENCE" file.
 */

declare(strict_types=1);

namespace Yassg\Files\Processors;

use League\CommonMark\ConverterInterface;
use Yassg\Configuration\Configuration;

use function Yassg\file_get_contents_safe;

use Yassg\Files\InputFileInterface;
use Yassg\Files\MutatedFile;

use function Yassg\preg_replace_safe;

class MarkdownProcessor implements ProcessorInterface
{
    private Configuration $configuration;
    private ConverterInterface $converter;

    public function __construct(
        ConverterInterface $converter,
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
            $this->converter->convert(
                $inputFile->getContent(),
            )->getContent();

        if (array_key_exists('twigTemplate', $inputFile->getMetadata())) {
            /** @var string $twigTemplate */
            $twigTemplate = $inputFile->getMetadata()['twigTemplate'];

            return new MutatedFile(
                $this->getTwigTemplateContent($twigTemplate),
                $inputFile
                    ->mergeMetadata(
                        ['renderedMarkdown' => $renderedMarkdown],
                    )
                    ->getMetadata(),
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
        return file_get_contents_safe(
            join(
                DIRECTORY_SEPARATOR,
                [
                    $this->configuration->getInputDirectory(),
                    $relativePathname,
                ],
            ),
        );
    }

    private function processPathname(string $pathname): string
    {
        $extension = pathinfo($pathname, PATHINFO_EXTENSION);

        return preg_replace_safe("/{$extension}$/i", 'html', $pathname);
    }
}
