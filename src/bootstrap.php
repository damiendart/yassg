<?php

// Copyright (C) 2021 Damien Dart, <damiendart@pobox.com>.
// This file is distributed under the MIT licence. For more information,
// please refer to the accompanying "LICENCE" file.

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use DI\ContainerBuilder;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\SingleCommandApplication;
use Yassg\Commands\BuildCommand;

$container = (new ContainerBuilder())->build();

(new SingleCommandApplication())
    ->setName('yassg')
    ->setHelp('Yet another static site generator.')
    ->addArgument(
        'inputDirectory',
        InputArgument::REQUIRED,
        'The input directory'
    )
    ->addArgument(
        'outputDirectory',
        InputArgument::REQUIRED,
        'The output directory'
    )
    ->setCode($container->get(BuildCommand::class))
    ->run();
