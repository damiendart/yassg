<?php

// Copyright (C) 2021 Damien Dart, <damiendart@pobox.com>.
// This file is distributed under the MIT licence. For more information,
// please refer to the accompanying "LICENCE" file.

declare(strict_types=1);

namespace Yassg\Application;

use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Console\Application as SymfonyApplication;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Yassg\Commands\BuildCommand;
use Yassg\Container\Container;

class Application extends SymfonyApplication
{
    const NAME = 'yassg';
    const VERSION = '0.1.0';

    public function __construct()
    {
        parent::__construct(static::NAME, static::VERSION);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function getCommandName(InputInterface $input): ?string
    {
        /** @var string $configurationFilepath */
        $configurationFilepath = $input->getOption('config');

        $this->initialise($configurationFilepath);

        return parent::getCommandName($input);
    }

    protected function getDefaultInputDefinition(): InputDefinition
    {
        $inputDefinition = parent::getDefaultInputDefinition();

        $inputDefinition->addOption(
            new InputOption(
                'config',
                'c',
                InputOption::VALUE_REQUIRED,
                'The path to a ' . static::NAME . ' configuration file.',
                getcwd() . DIRECTORY_SEPARATOR . '.yassg.php',
            ),
        );

        return $inputDefinition;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws Exception
     * @throws NotFoundExceptionInterface
     */
    private function initialise(string $configurationFilepath): void
    {
        $container = new Container($configurationFilepath);

        /** @var BuildCommand $buildCommand */
        $buildCommand = $container->get(BuildCommand::class);

        $this->add($buildCommand);
    }
}
