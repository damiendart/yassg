<?php

// Copyright (C) 2021 Damien Dart, <damiendart@pobox.com>.
// This file is distributed under the MIT licence. For more information,
// please refer to the accompanying "LICENCE" file.

declare(strict_types=1);

namespace Yassg\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Yassg\Configuration\Configuration;
use Yassg\Events\EventDispatcher;
use Yassg\Events\FileCopiedEvent;
use Yassg\Exceptions\InvalidConfigurationException;
use Yassg\Yassg;

class BuildCommand extends Command
{
    private EventDispatcher $eventDispatcher;
    private Yassg $yassg;

    public function __construct(
        EventDispatcher $eventDispatcher,
        Yassg $yassg,
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->yassg = $yassg;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('build')
            ->setDescription('Builds a site.')
            ->setDefinition(
                [
                    new InputOption(
                        'config',
                        'c',
                        InputOption::VALUE_REQUIRED,
                        'The path to a yassg configuration file.',
                    ),
                ],
            );
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output,
    ): int {
        $this->setupEventListeners($output);

        try {
            $this->yassg->build(
                $this->getConfiguration(
                    $input->getOption('config') ?? null,
                ),
            );
        } catch (InvalidConfigurationException $e) {
            $formatter = $this->getHelper('formatter');

            $output->writeln(
                $formatter->formatBlock(
                    ['[' . $e::class . ']', $e->getMessage()],
                    'error',
                    true,
                ),
            );

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    /**
     * @throws InvalidConfigurationException
     */
    private function getConfiguration(
        ?string $configurationFilepath = null,
    ): Configuration {
        if (null === $configurationFilepath) {
            $configurationFilepath = getcwd()
                . DIRECTORY_SEPARATOR
                . '.yassg.php';
        }

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
    }

    private function setupEventListeners(OutputInterface $output): void
    {
        $this->eventDispatcher->addEventListener(
            FileCopiedEvent::class,
            function (FileCopiedEvent $event) use ($output): void {
                $output->writeln(
                    "[✔] Copied file to \"{$event->getRealOutputFilepath()}\"",
                );

                if ($output->isVerbose()) {
                    $output->writeln(
                        "    <info>(Source file: \"{$event->getRealInputFilepath()}\")</info>",
                    );
                }
            },
        );
    }
}
