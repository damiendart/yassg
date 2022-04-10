<?php

// Copyright (c) 2022 Damien Dart, <damiendart@pobox.com>.
// This file is distributed under the MIT licence. For more information,
// please refer to the accompanying "LICENCE" file.

declare(strict_types=1);

namespace Yassg\Application;

use RuntimeException;
use Yassg\Application\Commands\BuildCommand;
use Yassg\Container\Container;
use Yassg\Exceptions\InvalidArgumentException;

class Application
{
    public const RETURN_SUCCESS = 0;
    public const RETURN_FAILURE = 1;

    public OutputInterface $output;

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    public function run(array $argv): int
    {
        try {
            $arguments = new ArgumentParser($argv);
        } catch (InvalidArgumentException $exception) {
            $this->output
                ->writeError($exception->getMessage() . PHP_EOL)
                ->writeError(
                    'Use the "--help" flag for more information.' . PHP_EOL,
                );

            return self::RETURN_FAILURE;
        }

        $configurationFilePathname = $arguments
            ->getConfigurationFilePathname();

        if (
            null === $configurationFilePathname
            && is_file(getcwd() . DIRECTORY_SEPARATOR . '.yassg.php')
        ) {
            $configurationFilePathname = getcwd()
                . DIRECTORY_SEPARATOR
                . '.yassg.php';
        }

        if ($arguments->isVerboseFlagSet()) {
            $this->output->setVerbosity(
                OutputInterface::VERBOSITY_VERBOSE,
            );
        }

        try {
            $container = new Container($configurationFilePathname);

            /** @var BuildCommand $buildCommand */
            $buildCommand = $container->get(BuildCommand::class);

            $buildCommand->run($this->output);
        } catch (RuntimeException $exception) {
            $this->output
                ->writeError('[' . $exception::class . ']' . PHP_EOL)
                ->writeError($exception->getMessage() . PHP_EOL);

            return self::RETURN_FAILURE;
        }

        return self::RETURN_SUCCESS;
    }
}
