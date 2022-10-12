<?php

/*
 * Copyright (c) 2022 Damien Dart, <damiendart@pobox.com>.
 * This file is distributed under the MIT licence. For more information,
 * please refer to the accompanying "LICENCE" file.
 */

declare(strict_types=1);

namespace Yassg\Container;

use DI\ContainerBuilder;
use Exception;
use Psr\Container\ContainerInterface;
use Yassg\Configuration\Configuration;
use Yassg\Configuration\InvalidConfigurationException;

class Container implements ContainerInterface
{
    /** @var string[] */
    private static array $defaultDefinitionsFilenames = [
        'files.php',
        'finder.php',
        'markdown.php',
        'processors.php',
    ];

    private ContainerInterface $container;

    /**
     * @throws Exception
     */
    public function __construct(?string $configurationFilePathname)
    {
        $this->initialiseContainer(
            $this->resolveConfiguration($configurationFilePathname),
        );
    }

    /** {@inheritDoc} */
    public function get($id): mixed
    {
        return $this->container->get($id);
    }

    /** {@inheritDoc} */
    public function has($id): bool
    {
        return $this->container->has($id);
    }

    /**
     * @throws Exception
     */
    private function initialiseContainer(Configuration $configuration): void
    {
        $containerBuilder = new ContainerBuilder();

        $containerBuilder->addDefinitions(
            [
                Configuration::class => function () use ($configuration): Configuration {
                    return $configuration;
                },
            ],
        );

        foreach (self::$defaultDefinitionsFilenames as $filename) {
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

    /**
     * @throws InvalidConfigurationException
     */
    private function resolveConfiguration(
        ?string $configurationFilePathname,
    ): Configuration {
        if (null === $configurationFilePathname) {
            return new Configuration(
                getcwd() . DIRECTORY_SEPARATOR . 'src',
                getcwd() . DIRECTORY_SEPARATOR . 'public',
            );
        }

        if (false === is_file($configurationFilePathname)) {
            throw new InvalidConfigurationException(
                sprintf(
                    '"%s" does not exist or is not a configuration file.',
                    $configurationFilePathname,
                ),
            );
        }

        $configuration = include $configurationFilePathname;

        if (false === $configuration instanceof Configuration) {
            throw new InvalidConfigurationException(
                sprintf(
                    'The config file "%s" does not return a "%s" instance.',
                    $configurationFilePathname,
                    Configuration::class,
                ),
            );
        }

        return $configuration;
    }
}
