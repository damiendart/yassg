<?php

// Copyright (C) 2021 Damien Dart, <damiendart@pobox.com>.
// This file is distributed under the MIT licence. For more information,
// please refer to the accompanying "LICENCE" file.

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Console\SingleCommandApplication;
use Yassg\Commands\BuildCommand;

(new SingleCommandApplication())
    ->setName('yassg')
    ->setHelp('Yet another static site generator.')
    ->setCode(new BuildCommand())
    ->run();
