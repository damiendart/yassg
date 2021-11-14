<?php

// Copyright (C) 2021 Damien Dart, <damiendart@pobox.com>.
// This file is distributed under the MIT licence. For more information,
// please refer to the accompanying "LICENCE" file.

declare(strict_types=1);

namespace Yassg\Files;

interface InputFileInterface
{
    public function getContent(): string;

    public function getOriginalAbsolutePathname(): string;

    public function getOriginalInputFile(): InputFileInterface;

    public function getRelativePathname(): string;
}
