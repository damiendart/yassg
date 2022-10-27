<?php

/*
 * Copyright (c) 2022 Damien Dart, <damiendart@pobox.com>.
 * This file is distributed under the MIT licence. For more information,
 * please refer to the accompanying "LICENCE" file.
 */

declare(strict_types=1);

namespace Yassg\Application;

use Throwable;
use Yassg\Application\Commands\BuildCommand;
use Yassg\Application\Commands\CommandInterface;
use Yassg\Application\Commands\HelpCommand;
use Yassg\Container\Container;

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

    /** @param string[] $argv */
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

            /** @var CommandInterface $command */
            $command = $container->get(
                match (true) {
                    $arguments->isHelpFlagSet() => HelpCommand::class,
                    default => BuildCommand::class,
                },
            );

            $command->run($this->output);
        } catch (Throwable $throwable) {
            $this->renderThrowable($throwable);

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

    private function renderThrowable(Throwable $throwable): void
    {
        $this->output
            ->writeError('[' . $throwable::class . ']' . PHP_EOL)
            ->writeError($throwable->getMessage() . PHP_EOL);

        $this->output->writeError(PHP_EOL);

        if ($this->output->isVerbose()) {
            $this->output->writeError('Stacktrace:' . PHP_EOL);
            $this->output->writeError($throwable->getTraceAsString() . PHP_EOL);

            if (null !== $throwable->getPrevious()) {
                $this->output->writeError(PHP_EOL);
                $this->renderThrowable($throwable->getPrevious());
            }
        } else {
            $this->output->writeError(
                'Enable verbose mode to display stacktrace.' . PHP_EOL,
            );
        }
    }
}
