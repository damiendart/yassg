<?php

// Copyright (C) 2021 Damien Dart, <damiendart@pobox.com>.
// This file is distributed under the MIT licence. For more information,
// please refer to the accompanying "LICENCE" file.

declare(strict_types=1);

namespace Yassg;

use Yassg\Events\EventDispatcher;
use Yassg\Events\TestEvent;

class Yassg
{
    private EventDispatcher $eventDispatcher;

    public function __construct(
        EventDispatcher $eventDispatcher,
    ) {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function build(
        string $inputDirectory,
        string $outputDirectory,
    ): void {
        $this->eventDispatcher->dispatch(new TestEvent($inputDirectory));
        $this->eventDispatcher->dispatch(new TestEvent($outputDirectory));
    }
}
