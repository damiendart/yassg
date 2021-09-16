<?php

// Copyright (C) 2021 Damien Dart, <damiendart@pobox.com>.
// This file is distributed under the MIT licence. For more information,
// please refer to the accompanying "LICENCE" file.

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use DI\ContainerBuilder;
use Symfony\Component\Console\Application;
use Yassg\Commands\BuildCommand;

$application = new Application('yassg');
$container = (new ContainerBuilder())->build();

$application->add($container->get(BuildCommand::class));
$application->run();
