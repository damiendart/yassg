<?php

/*
 * Copyright (c) 2022 Damien Dart, <damiendart@pobox.com>.
 * This file is distributed under the MIT licence. For more information,
 * please refer to the accompanying "LICENCE" file.
 */

declare(strict_types=1);

namespace Yassg\Application\Commands;

use Yassg\Application\OutputInterface;

class HelpCommand implements CommandInterface
{
    public function run(OutputInterface $output): void
    {
        $output->write(
            <<<'HELP'
            Usage: yassg [flags]

            Yet Another Static Site Generator.


            FLAGS:

            -c FILE, --config=FILE
                Specify a custom location to a yassg configuration file (by default
                yassg will attempt to load ".yassg.php" in the current directory).

            -h, --help
                Display this help text and exit.

            -v, --verbose
                Increase verbosity of command output (setting the YASSG_VERBOSE
                environment variable will also enable verbose mode).


            POSITIONAL ARGUMENTS

            This application does not use positional arguments.

            HELP
        );

        if ($output->isVerbose()) {
            // A few newlines are added to separate the help text and
            // the yassg runtime statistics.
            $output->write("\n\n");
        }
    }
}
