<?php

// Copyright (c) 2022 Damien Dart, <damiendart@pobox.com>.
// This file is distributed under the MIT licence. For more information,
// please refer to the accompanying "LICENCE" file.

declare(strict_types=1);

namespace Yassg\Container;

use DI\ContainerBuilder;
use Exception;
use Psr\Container\ContainerInterface;
use Yassg\Configuration\Configuration;
use Yassg\Exceptions\InvalidConfigurationException;

class Container implements ContainerInterface
{
    /** @var string[] */
    private static array $defaultDefinitionsFilenames = [
        'files.php',
        'markdown.php',
        'processors.php',
    ];

    private ContainerInterface $container;

    /**
     * @throws Exception
     */
    public function __construct(string $configurationFilePathname)
    {
        $this->initialise($configurationFilePathname);
    }

    public function get(string $id): mixed
    {
        return $this->container->get($id);
    }

    public function has(string $id): bool
    {
        return $this->container->has($id);
    }

    /**
     * @throws Exception
     */
    private function initialise(string $configurationFilePathname): void
    {
        if (false === file_exists($configurationFilePathname)) {
            throw new InvalidConfigurationException(
                sprintf(
                    'The config file "%s" does not exist.',
                    $configurationFilePathname,
                ),
            );
        }

        $configuration = include $configurationFilePathname;
        $containerBuilder = new ContainerBuilder();

        if (false === $configuration instanceof Configuration) {
            throw new InvalidConfigurationException(
                sprintf(
                    'The config file "%s" does not return a "%s" instance.',
                    $configurationFilePathname,
                    Configuration::class,
                ),
            );
        }

        $containerBuilder->addDefinitions(
            [
                Configuration::class => function () use ($configuration): Configuration {
                    return $configuration;
                },
            ],
        );

        foreach (static::$defaultDefinitionsFilenames as $filename) {
            $containerBuilder->addDefinitions(
                join(
                    DIRECTORY_SEPARATOR,
                    [__DIR__, 'Definitions', $filename],
                ),
            );
        }

        foreach ($configuration->getPlugins() as $plugin) {
            $containerBuilder->addDefinitions(
                $plugin->getContainerDefinitions(),
            );
        }

        $this->container = $containerBuilder->build();
    }
}
