<?php

// Copyright (C) 2021 Damien Dart, <damiendart@pobox.com>.
// This file is distributed under the MIT licence. For more information,
// please refer to the accompanying "LICENCE" file.

declare(strict_types=1);

namespace Yassg\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Yassg\Events\EventDispatcher;
use Yassg\Events\FileCopiedEvent;
use Yassg\Yassg;

class BuildCommand
{
    private Yassg $yassg;
    private EventDispatcher $eventDispatcher;

    public function __construct(
        Yassg $yassg,
        EventDispatcher $eventDispatcher
    ) {
        $this->yassg = $yassg;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke(
        InputInterface $input,
        OutputInterface $output
    ): int {
        $this->setupEventListeners($output);

        $this->yassg->build(
            $input->getArgument('inputDirectory'),
            $input->getArgument('outputDirectory'),
        );

        return Command::SUCCESS;
    }

    public function setupEventListeners(OutputInterface $output): void
    {
        $this->eventDispatcher->addEventListener(
            FileCopiedEvent::class,
            function (FileCopiedEvent $event) use ($output): void {
                $output->writeln(
                    "[✔] Copied file to \"{$event->getRealFilepath()}\"",
                );
            }
        );
    }
}
