<?php

/*
 * Copyright (C) Damien Dart, <damiendart@pobox.com>.
 * This file is distributed under the MIT licence. For more information,
 * please refer to the accompanying "LICENCE" file.
 */

declare(strict_types=1);

namespace Yassg\Events;

use Yassg\Files\InputFileCollection;

class PreSiteBuildEvent extends AbstractEvent
{
    private InputFileCollection $inputFiles;

    public function __construct(InputFileCollection $inputFiles)
    {
        $this->inputFiles = $inputFiles;
    }

    public function getInputFiles(): InputFileCollection
    {
        return $this->inputFiles;
    }
}
