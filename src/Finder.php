<?php

/*
 * Copyright (C) Damien Dart, <damiendart@pobox.com>.
 * This file is distributed under the MIT licence. For more information,
 * please refer to the accompanying "LICENCE" file.
 */

declare(strict_types=1);

namespace Yassg;

use Symfony\Component\Finder\Finder as BaseFinder;

/**
 * A Finder used to find all input files to process.
 */
class Finder extends BaseFinder
{
    /**
     * @psalm-api
     */
    public function __construct()
    {
        parent::__construct();

        $this->ignoreDotFiles(true);
    }
}
