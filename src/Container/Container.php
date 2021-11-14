<?php

// Copyright (C) 2021 Damien Dart, <damiendart@pobox.com>.
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
        $containerBuilder = new ContainerBuilder();

        $containerBuilder->addDefinitions(
            [
                Configuration::class => function () use ($configurationFilePathname) {
                    if (false === file_exists($configurationFilePathname)) {
                        throw new InvalidConfigurationException(
                            sprintf(
                                'The config file "%s" does not exist.',
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

        $this->container = $containerBuilder->build();
    }
}
