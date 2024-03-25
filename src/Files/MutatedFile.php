<?php

/*
 * Copyright (C) Damien Dart, <damiendart@pobox.com>.
 * This file is distributed under the MIT licence. For more information,
 * please refer to the accompanying "LICENCE" file.
 */

declare(strict_types=1);

namespace Yassg\Files;

use Yassg\Metadata\MetadataTrait;

class MutatedFile implements InputFileInterface
{
    use MetadataTrait;

    private string $content;
    private InputFileInterface $originalInputFile;
    private string $relativePathname;

    /** @param array<array-key, mixed> $metadata */
    public function __construct(
        string $content,
        array $metadata,
        InputFileInterface $originalInputFile,
        string $relativePathname,
    ) {
        $this->originalInputFile = $originalInputFile;
        $this->relativePathname = $relativePathname;

        $this->setContent($content);
        $this->setMetadata($metadata);
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

    // This method is final to appease Psalm; for more information,
    // please see <https://psalm.dev/074>.
    final public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }
}
