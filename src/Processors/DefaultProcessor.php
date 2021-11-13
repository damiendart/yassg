<?php

// Copyright (C) 2021 Damien Dart, <damiendart@pobox.com>.
// This file is distributed under the MIT licence. For more information,
// please refer to the accompanying "LICENCE" file.

declare(strict_types=1);

namespace Yassg\Processors;

use Yassg\Files\CopyFile;
use Yassg\Files\InputFile;
use Yassg\Files\InputFileInterface;
use Yassg\Files\OutputFileInterface;
use Yassg\Files\WriteFile;

class DefaultProcessor implements ProcessorInterface
{
    public function canProcess(InputFileInterface $file): bool
    {
        return true;
    }

    public function process(InputFileInterface $inputFile): OutputFileInterface
    {
        if ($inputFile instanceof InputFile) {
            return new CopyFile($inputFile);
        }

        return new WriteFile(
            $inputFile->getContent(),
            $inputFile->getRelativeFilepath(),
        );
    }
}
