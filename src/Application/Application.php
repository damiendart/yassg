<?php

// Copyright (c) 2022 Damien Dart, <damiendart@pobox.com>.
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
use Yassg\Application\Commands\BuildCommand;
use Yassg\Container\Container;

class Application extends SymfonyApplication
{
    const NAME = 'yassg';
    const VERSION = '0.1.0';

    public function __construct()
    {
        parent::__construct(self::NAME, self::VERSION);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function getCommandName(InputInterface $input): ?string
    {
        /** @var string $configurationFilePathname */
        $configurationFilePathname = $input->getOption('config');

        $this->initialise($configurationFilePathname);

        return parent::getCommandName($input);
    }

    protected function getDefaultInputDefinition(): InputDefinition
    {
        $inputDefinition = parent::getDefaultInputDefinition();

        $inputDefinition->addOption(
            new InputOption(
                'config',
                'c',
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_REQUIRED,
                'The pathname to a ' . self::NAME . ' configuration file.',
                null,
            ),
        );

        return $inputDefinition;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws Exception
     * @throws NotFoundExceptionInterface
     */
    private function initialise(?string $configurationFilePathname): void
    {
        $container = new Container($configurationFilePathname);

        /** @var BuildCommand $buildCommand */
        $buildCommand = $container->get(BuildCommand::class);

        $this->add($buildCommand);
    }
}
