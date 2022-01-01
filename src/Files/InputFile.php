<?php

// Copyright (C) 2021 Damien Dart, <damiendart@pobox.com>.
// This file is distributed under the MIT licence. For more information,
// please refer to the accompanying "LICENCE" file.

declare(strict_types=1);

namespace Yassg\Files;

use Symfony\Component\Finder\SplFileInfo;
use Yassg\Traits\HasMetadata;

class InputFile implements InputFileInterface
{
    use HasMetadata;

    private ?string $content = null;
    private SplFileInfo $file;

    public function __construct(SplFileInfo $file)
    {
        $this->file = $file;
    }

    public function getContent(): string
    {
        return $this->content ?? $this->file->getContents();
    }

    public function getFileInfo(): SplFileInfo
    {
        return $this->file;
    }

    public function getOriginalAbsolutePathname(): string
    {
        return $this->file->getRealPath();
    }

    public function getOriginalInputFile(): InputFileInterface
    {
        return $this;
    }

    public function getRelativePathname(): string
    {
        return $this->file->getRelativePathname();
    }

    /**
     *  Returns whether the content of an input file has been pre-processed.
     *
     *  Input files can be pre-processed as part of metadata population;
     *  for instance, if an input file has [front matter][] it is parsed
     *  and stripped out before any further processing.
     *
     *    [front matter]: <https://gohugo.io/content-management/front-matter/>
     */
    public function isDirty(): bool
    {
        return null !== $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }
}
