<?php

/*
 * Copyright (c) 2022 Damien Dart, <damiendart@pobox.com>.
 * This file is distributed under the MIT licence. For more information,
 * please refer to the accompanying "LICENCE" file.
 */

declare(strict_types=1);

namespace Yassg\Application\Commands;

use Yassg\Application\OutputInterface;
use Yassg\BuildException;
use Yassg\Configuration\Configuration;
use Yassg\Events\EventDispatcher;
use Yassg\Events\FileCopiedEvent;
use Yassg\Events\FileEventInterface;
use Yassg\Events\FileWrittenEvent;
use Yassg\Yassg;

class BuildCommand implements CommandInterface
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

    /**
     * @throws \Throwable
     */
    public function run(OutputInterface $output): void
    {
        $this->setupEventListeners($output);

        try {
            $this->yassg->build($this->configuration);
        } catch (BuildException $exception) {
            $output->write("[x] {$exception->getMessage()}" . PHP_EOL . PHP_EOL);

            if (null !== $exception->getPrevious()) {
                throw $exception->getPrevious();
            }
        }

        if ($output->isVerbose()) {
            $output->write(PHP_EOL);
        }

        $output->write(
            sprintf(
                '%d file%s created' . PHP_EOL,
                $this->createdFileCount,
                1 === $this->createdFileCount ? '' : 's',
            ),
        );
    }

    private function handleFileEvent(
        FileEventInterface $event,
        OutputInterface $output,
    ): void {
        $action = ($event instanceof FileCopiedEvent)
            ? 'Copied file to'
            : 'Written';

        ++$this->createdFileCount;

        if ($output->isVerbose()) {
            $output
                ->write(
                    "[✔] {$action} \"{$event->getOutputAbsolutePathname()}\"" . PHP_EOL,
                )
                ->write(
                    "    (Source file: \"{$event->getInputAbsolutePathname()}\")" . PHP_EOL,
                );
        }
    }

    private function setupEventListeners(OutputInterface $output): void
    {
        $this->eventDispatcher->addEventListener(
            [FileCopiedEvent::class, FileWrittenEvent::class],
            function (FileEventInterface $event) use ($output): void {
                $this->handleFileEvent($event, $output);
            },
        );
    }
}
