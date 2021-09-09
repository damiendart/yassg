<?php

// Copyright (C) 2021 Damien Dart, <damiendart@pobox.com>.
// This file is distributed under the MIT licence. For more information,
// please refer to the accompanying "LICENCE" file.

declare(strict_types=1);

namespace Yassg\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BuildCommand
{
    public function __construct()
    {
        // ...
    }

    public function __invoke(
        InputInterface $input,
        OutputInterface $output
    ): int {
        $output->writeln(self::class);

        return Command::SUCCESS;
    }
}
