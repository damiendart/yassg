<?php

// Copyright (C) 2021 Damien Dart, <damiendart@pobox.com>.
// This file is distributed under the MIT licence. For more information,
// please refer to the accompanying "LICENCE" file.

declare(strict_types=1);

namespace Yassg\Processors;

use Yassg\Files\CopyFile;
use Yassg\Files\InputFile;

class DefaultProcessor implements ProcessorInterface
{
    public function canProcess(InputFile $file): bool
    {
        return true;
    }

    public function process(InputFile $inputFile): CopyFile
    {
        return new CopyFile($inputFile);
    }
}
