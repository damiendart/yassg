<?php

// Copyright (C) 2021 Damien Dart, <damiendart@pobox.com>.
// This file is distributed under the MIT licence. For more information,
// please refer to the accompanying "LICENCE" file.

declare(strict_types=1);

namespace Yassg\Tests\Unit\Events;

use PHPUnit\Framework\TestCase;
use Yassg\Events\Event;

/**
 * @internal
 * @coversNothing
 */
class EventTest extends TestCase
{
    public function testSettingEventPropagating(): void
    {
        $event = new Event();

        $this->assertFalse($event->isPropagationStopped());

        $event->stopPropagation();

        $this->assertTrue($event->isPropagationStopped());
    }
}