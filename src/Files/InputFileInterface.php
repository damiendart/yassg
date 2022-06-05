<?php

/*
 * Copyright (c) 2022 Damien Dart, <damiendart@pobox.com>.
 * This file is distributed under the MIT licence. For more information,
 * please refer to the accompanying "LICENCE" file.
 */

declare(strict_types=1);

namespace Yassg\Files;

interface InputFileInterface
{
    public function getContent(): string;

    public function getMetadata(): array;

    public function getOriginalAbsolutePathname(): string;

    public function getOriginalInputFile(): InputFileInterface;

    public function getRelativePathname(): string;

    public function setContent(string $content): self;

    public function setMetadata(array $metadata): self;
}
