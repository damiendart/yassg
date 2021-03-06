<?php

/*
 * Copyright (c) 2022 Damien Dart, <damiendart@pobox.com>.
 * This file is distributed under the MIT licence. For more information,
 * please refer to the accompanying "LICENCE" file.
 */

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

    private ?float $startTime;

    public function __construct(
        OutputInterface $output,
        ?float $startTime = null,
    ) {
        $this->output = $output;
        $this->startTime = $startTime;
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
            && is_file($this->getDefaultConfigurationFilePathname())
        ) {
            $configurationFilePathname =
                $this->getDefaultConfigurationFilePathname();
        }

        if (
            $arguments->isVerboseFlagSet()
            || false !== getenv('YASSG_VERBOSE')
        ) {
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

        if (is_float($this->startTime) && $this->output->isVerbose()) {
            $this->output->write(
                sprintf(
                    'yassg took %d ms, %.2F MiB memory used' . PHP_EOL,
                    round((microtime(true) - $this->startTime) * 1000),
                    memory_get_usage(true) / 1024 / 1024,
                ),
            );
        }

        return self::RETURN_SUCCESS;
    }

    private function getDefaultConfigurationFilePathname(): string
    {
        return getcwd() . DIRECTORY_SEPARATOR . '.yassg.php';
    }
}
