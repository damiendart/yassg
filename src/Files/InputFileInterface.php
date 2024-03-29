<?php

/*
 * Copyright (C) Damien Dart, <damiendart@pobox.com>.
 * This file is distributed under the MIT licence. For more information,
 * please refer to the accompanying "LICENCE" file.
 */

declare(strict_types=1);

namespace Yassg\Files;

interface InputFileInterface
{
    public function getContent(): string;

    /** @return array<array-key, mixed> */
    public function getMetadata(): array;

    public function getOriginalAbsolutePathname(): string;

    public function getOriginalInputFile(): InputFileInterface;

    public function getRelativePathname(): string;

    /** @param array<array-key, mixed> $metadata */
    public function mergeMetadata(array $metadata): self;

    /**
     * @psalm-api
     */
    public function setContent(string $content): self;

    /**
     * @param array<array-key, mixed> $metadata
     *
     * @psalm-api
     */
    public function setMetadata(array $metadata): self;
}
