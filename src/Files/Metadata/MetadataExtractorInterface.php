<?php

// Copyright (C) 2021 Damien Dart, <damiendart@pobox.com>.
// This file is distributed under the MIT licence. For more information,
// please refer to the accompanying "LICENCE" file.

declare(strict_types=1);

namespace Yassg\Files\Metadata;

use Yassg\Files\InputFile;

interface MetadataExtractorInterface
{
    public function extractFromInputFile(InputFile $inputFile): array;
}
