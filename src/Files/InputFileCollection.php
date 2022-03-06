<?php

// Copyright (c) 2022 Damien Dart, <damiendart@pobox.com>.
// This file is distributed under the MIT licence. For more information,
// please refer to the accompanying "LICENCE" file.

declare(strict_types=1);

namespace Yassg\Files;

use ArrayIterator;
use IteratorAggregate;

class InputFileCollection implements IteratorAggregate
{
    /** @var InputFile[] */
    private array $inputFiles = [];

    public function addInputFile(InputFile $inputFile): self
    {
        $this->inputFiles[] = $inputFile;

        return $this;
    }

    /** @return ArrayIterator<array-key, InputFile> */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->inputFiles);
    }
}
