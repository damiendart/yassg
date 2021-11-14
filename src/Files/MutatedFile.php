<?php

// Copyright (C) 2021 Damien Dart, <damiendart@pobox.com>.
// This file is distributed under the MIT licence. For more information,
// please refer to the accompanying "LICENCE" file.

declare(strict_types=1);

namespace Yassg\Files;

class MutatedFile implements InputFileInterface
{
    private string $content;
    private InputFileInterface $originalInputFile;
    private string $relativePathname;

    public function __construct(
        string $content,
        InputFileInterface $originalInputFile,
        string $relativePathname,
    ) {
        $this->content = $content;
        $this->originalInputFile = $originalInputFile;
        $this->relativePathname = $relativePathname;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getOriginalAbsolutePathname(): string
    {
        return $this->originalInputFile->getOriginalAbsolutePathname();
    }

    public function getOriginalInputFile(): InputFileInterface
    {
        return $this->originalInputFile->getOriginalInputFile();
    }

    public function getRelativePathname(): string
    {
        return $this->relativePathname;
    }
}
