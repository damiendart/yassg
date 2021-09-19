<?php

// Copyright (C) 2021 Damien Dart, <damiendart@pobox.com>.
// This file is distributed under the MIT licence. For more information,
// please refer to the accompanying "LICENCE" file.

declare(strict_types=1);

namespace Yassg\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Yassg\Configuration\Configuration;
use Yassg\Events\EventDispatcher;
use Yassg\Events\FileCopiedEvent;
use Yassg\Exceptions\InvalidInputDirectoryException;
use Yassg\Yassg;

class BuildCommand extends Command
{
    private Configuration $configuration;
    private EventDispatcher $eventDispatcher;
    private Yassg $yassg;

    public function __construct(
        Configuration $configuration,
        EventDispatcher $eventDispatcher,
        Yassg $yassg,
    ) {
        $this->configuration = $configuration;
        $this->eventDispatcher = $eventDispatcher;
        $this->yassg = $yassg;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('build')
            ->setDescription(
                'Takes an input directory and uses it to build a site.',
            )
            ->addArgument(
                'inputDirectory',
                InputArgument::REQUIRED,
                'The input directory',
            )
            ->addArgument(
                'outputDirectory',
                InputArgument::REQUIRED,
                'The output directory',
            );
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output,
    ): int {
        $this->setupEventListeners($output);

        try {
            $this->yassg->build(
                $input->getArgument('inputDirectory'),
                $input->getArgument('outputDirectory'),
                $this->configuration->getProcessors(),
            );
        } catch (InvalidInputDirectoryException $e) {
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

    protected function setupEventListeners(OutputInterface $output): void
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
