<?php

/*
 * Copyright (C) Damien Dart, <damiendart@pobox.com>.
 * This file is distributed under the MIT licence. For more information,
 * please refer to the accompanying "LICENCE" file.
 */

declare(strict_types=1);

namespace Yassg\Files;

use function Yassg\file_get_contents_safe;

use Yassg\Metadata\MetadataTrait;

class InputFile implements InputFileInterface
{
    use MetadataTrait;

    private string $absolutePathname;
    private ?string $content = null;
    private string $relativePathname;

    public function __construct(
        string $absolutePathname,
        string $relativePathname,
    ) {
        $this->absolutePathname = $absolutePathname;
        $this->relativePathname = $relativePathname;
    }

    public function getContent(): string
    {
        return $this->content ?? $this->readFile();
    }

    public function getOriginalAbsolutePathname(): string
    {
        return $this->absolutePathname;
    }

    public function getOriginalInputFile(): InputFileInterface
    {
        return $this;
    }

    public function getRelativePathname(): string
    {
        return $this->relativePathname;
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

    private function readFile(): string
    {
        return file_get_contents_safe($this->absolutePathname);
    }
}
