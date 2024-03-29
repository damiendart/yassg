<?php

/*
 * Copyright (C) Damien Dart, <damiendart@pobox.com>.
 * This file is distributed under the MIT licence. For more information,
 * please refer to the accompanying "LICENCE" file.
 */

declare(strict_types=1);

namespace Yassg\Tests\Unit\Events;

use PHPUnit\Framework\TestCase;
use Yassg\Events\AbstractEvent;

/**
 * @covers \Yassg\Events\AbstractEvent
 *
 * @internal
 */
class EventTest extends TestCase
{
    public function testSettingEventPropagating(): void
    {
        $event = new class () extends AbstractEvent {};

        $this->assertFalse($event->isPropagationStopped());

        $event->stopPropagation();

        $this->assertTrue($event->isPropagationStopped());
    }
}
