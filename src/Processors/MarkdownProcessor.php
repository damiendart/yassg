<?php

// Copyright (C) 2021 Damien Dart, <damiendart@pobox.com>.
// This file is distributed under the MIT licence. For more information,
// please refer to the accompanying "LICENCE" file.

declare(strict_types=1);

namespace Yassg\Processors;

use League\CommonMark\MarkdownConverterInterface;
use Yassg\Files\InputFile;
use Yassg\Files\WriteFile;

class MarkdownProcessor implements ProcessorInterface
{
    private MarkdownConverterInterface $converter;

    public function __construct(MarkdownConverterInterface $converter)
    {
        $this->converter = $converter;
    }

    public function canProcess(InputFile $file): bool
    {
        return str_ends_with($file->getRealFilepath(), 'md');
    }

    public function process(InputFile $inputFile): WriteFile
    {
        return new WriteFile(
            $this->converter
                ->convertToHtml(
                    file_get_contents($inputFile->getRealFilepath()),
                )
                ->getContent(),
            $this->processFilepath($inputFile->getRelativeFilepath()),
        );
    }

    private function processFilepath(string $filepath): string
    {
        $extension = pathinfo($filepath, PATHINFO_EXTENSION);

        return preg_replace("/{$extension}$/i", 'html', $filepath);
    }
}
