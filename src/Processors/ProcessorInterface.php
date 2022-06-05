<?php

/*
 * Copyright (c) 2022 Damien Dart, <damiendart@pobox.com>.
 * This file is distributed under the MIT licence. For more information,
 * please refer to the accompanying "LICENCE" file.
 */

declare(strict_types=1);

namespace Yassg\Processors;

use Yassg\Files\InputFileInterface;
use Yassg\Files\OutputFileInterface;

interface ProcessorInterface
{
    public function canProcess(InputFileInterface $file): bool;

    public function process(InputFileInterface $inputFile): InputFileInterface|OutputFileInterface;
}
