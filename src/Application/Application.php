<?php

// Copyright (C) 2021 Damien Dart, <damiendart@pobox.com>.
// This file is distributed under the MIT licence. For more information,
// please refer to the accompanying "LICENCE" file.

declare(strict_types=1);

namespace Yassg\Application;

use DI\ContainerBuilder;
use Exception;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application as SymfonyApplication;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Yassg\Commands\BuildCommand;
use Yassg\Configuration\Configuration;
use Yassg\Exceptions\InvalidConfigurationException;

class Application extends SymfonyApplication
{
    const NAME = 'yassg';
    const VERSION = '0.1.0';

    private ContainerBuilder $containerBuilder;

    public function __construct()
    {
        parent::__construct(static::NAME, static::VERSION);

        $this->containerBuilder = new ContainerBuilder();
    }

    /**
     * @throws Exception
     */
    protected function getCommandName(InputInterface $input): ?string
    {
        $this->initialise($input->getOption('config'));

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
     * @throws Exception
     */
    private function initialise(string $configurationFilepath): void
    {
        $this->containerBuilder->addDefinitions(
            [
                Configuration::class => function (ContainerInterface $c) use ($configurationFilepath) {
                    if (false === file_exists($configurationFilepath)) {
                        throw new InvalidConfigurationException(
                            sprintf(
                                'The config file "%s" does not exist.',
                                $configurationFilepath,
                            ),
                        );
                    }

                    $configuration = include $configurationFilepath;

                    if (false === $configuration instanceof Configuration) {
                        throw new InvalidConfigurationException(
                            sprintf(
                                'The config file "%s" does not return a "%s" instance.',
                                $configurationFilepath,
                                Configuration::class,
                            ),
                        );
                    }

                    return $configuration;
                },
            ],
        );

        $container = $this->containerBuilder->build();

        $this->add($container->get(BuildCommand::class));
    }
}
