<?php

// Copyright (C) 2021 Damien Dart, <damiendart@pobox.com>.
// This file is distributed under the MIT licence. For more information,
// please refer to the accompanying "LICENCE" file.

declare(strict_types=1);

namespace Yassg\Processors;

use League\CommonMark\MarkdownConverterInterface;
use Yassg\Files\InputFileInterface;
use Yassg\Files\MutatedFile;

class MarkdownProcessor implements ProcessorInterface
{
    private MarkdownConverterInterface $converter;

    public function __construct(MarkdownConverterInterface $converter)
    {
        $this->converter = $converter;
    }

    public function canProcess(InputFileInterface $file): bool
    {
        return str_ends_with($file->getRelativeFilepath(), 'md');
    }

    public function process(InputFileInterface $inputFile): MutatedFile
    {
        return new MutatedFile(
            $this->converter
                ->convertToHtml($inputFile->getContent())
                ->getContent(),
            $inputFile->getOriginalInputFile(),
            $this->processFilepath($inputFile->getRelativeFilepath()),
        );
    }

    private function processFilepath(string $filepath): string
    {
        $extension = pathinfo($filepath, PATHINFO_EXTENSION);

        return preg_replace("/{$extension}$/i", 'html', $filepath);
    }
}
