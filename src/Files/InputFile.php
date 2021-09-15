<?php

// Copyright (C) 2021 Damien Dart, <damiendart@pobox.com>.
// This file is distributed under the MIT licence. For more information,
// please refer to the accompanying "LICENCE" file.

declare(strict_types=1);

namespace Yassg\Files;

use Symfony\Component\Finder\SplFileInfo;

class InputFile
{
    private SplFileInfo $file;

    public function __construct(SplFileInfo $file)
    {
        $this->file = $file;
    }

    public function getRelativeFilepath(): string
    {
        return $this->file->getRelativePathname();
    }

    public function getRealFilepath(): string
    {
        return $this->file->getRealPath();
    }
}
