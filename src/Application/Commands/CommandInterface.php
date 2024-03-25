<?php

/*
 * Copyright (C) Damien Dart, <damiendart@pobox.com>.
 * This file is distributed under the MIT licence. For more information,
 * please refer to the accompanying "LICENCE" file.
 */

declare(strict_types=1);

namespace Yassg\Application\Commands;

use Yassg\Application\OutputInterface;

interface CommandInterface
{
    public function run(OutputInterface $output): void;
}
