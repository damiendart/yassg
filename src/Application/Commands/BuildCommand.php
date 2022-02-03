<?php

// Copyright (c) 2022 Damien Dart, <damiendart@pobox.com>.
// This file is distributed under the MIT licence. For more information,
// please refer to the accompanying "LICENCE" file.

declare(strict_types=1);

namespace Yassg\Application\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Yassg\Configuration\Configuration;
use Yassg\Events\EventDispatcher;
use Yassg\Events\FileCopiedEvent;
use Yassg\Events\FileWrittenEvent;
use Yassg\Exceptions\InvalidConfigurationException;
use Yassg\Yassg;

class BuildCommand extends Command
{
    private int $createdFileCount = 0;
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
            ->setDescription('Builds a site.');
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output,
    ): int {
        $this->setupEventListeners($output);

        try {
            $this->yassg->build($this->configuration);
        } catch (InvalidConfigurationException $e) {
            /** @var FormatterHelper $formatter */
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

        $output->writeln(
            [
                '',
                sprintf(
                    '%d file%s created',
                    $this->createdFileCount,
                    1 === $this->createdFileCount ? '' : 's',
                ),
            ],
        );

        return Command::SUCCESS;
    }

    private function setupEventListeners(OutputInterface $output): void
    {
        $this->eventDispatcher->addEventListener(
            FileCopiedEvent::class,
            function (FileCopiedEvent $event) use ($output): void {
                ++$this->createdFileCount;
                $output->writeln(
                    "[✔] Copied file to \"{$event->getOutputAbsolutePathname()}\"",
                );

                if ($output->isVerbose()) {
                    $output->writeln(
                        "    <info>(Source file: \"{$event->getInputAbsolutePathname()}\")</info>",
                    );
                }
            },
        );

        $this->eventDispatcher->addEventListener(
            FileWrittenEvent::class,
            function (FileWrittenEvent $event) use ($output): void {
                ++$this->createdFileCount;
                $output->writeln(
                    "[✔] Written \"{$event->getOutputAbsolutePathname()}\"",
                );

                if ($output->isVerbose()) {
                    $output->writeln(
                        "    <info>(Source file: \"{$event->getInputAbsolutePathname()}\")</info>",
                    );
                }
            },
        );
    }
}
