<?php

// Copyright (C) 2021 Damien Dart, <damiendart@pobox.com>.
// This file is distributed under the MIT licence. For more information,
// please refer to the accompanying "LICENCE" file.

declare(strict_types=1);

namespace Yassg\Processors;

use Yassg\Files\InputFile;
use Yassg\Files\OutputFileInterface;

interface ProcessorInterface
{
    public function canProcess(InputFile $file): bool;

    public function process(InputFile $inputFile): OutputFileInterface;
}
