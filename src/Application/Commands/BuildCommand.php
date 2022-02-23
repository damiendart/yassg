<?php

// Copyright (c) 2022 Damien Dart, <damiendart@pobox.com>.
// This file is distributed under the MIT licence. For more information,
// please refer to the accompanying "LICENCE" file.

declare(strict_types=1);

namespace Yassg\Application\Commands;

use Yassg\Application\OutputInterface;
use Yassg\Configuration\Configuration;
use Yassg\Events\EventDispatcher;
use Yassg\Events\FileCopiedEvent;
use Yassg\Events\FileWrittenEvent;
use Yassg\Yassg;

class BuildCommand
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
    }

    public function run(OutputInterface $output): void
    {
        $this->setupEventListeners($output);

        $this->yassg->build($this->configuration);

        $output
            ->write(PHP_EOL)
            ->write(
                sprintf(
                    '%d file%s created' . PHP_EOL,
                    $this->createdFileCount,
                    1 === $this->createdFileCount ? '' : 's',
                ),
            );
    }

    private function setupEventListeners(OutputInterface $output): void
    {
        $this->eventDispatcher->addEventListener(
            FileCopiedEvent::class,
            function (FileCopiedEvent $event) use ($output): void {
                ++$this->createdFileCount;
                $output->write(
                    "[✔] Copied file to \"{$event->getOutputAbsolutePathname()}\"" . PHP_EOL,
                );

                if ($output->isVerbose()) {
                    $output->write(
                        "    (Source file: \"{$event->getInputAbsolutePathname()}\")" . PHP_EOL,
                    );
                }
            },
        );

        $this->eventDispatcher->addEventListener(
            FileWrittenEvent::class,
            function (FileWrittenEvent $event) use ($output): void {
                ++$this->createdFileCount;
                $output->write(
                    "[✔] Written \"{$event->getOutputAbsolutePathname()}\"" . PHP_EOL,
                );

                if ($output->isVerbose()) {
                    $output->write(
                        "    (Source file: \"{$event->getInputAbsolutePathname()}\")" . PHP_EOL,
                    );
                }
            },
        );
    }
}
