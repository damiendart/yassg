<?php

// Copyright (c) 2022 Damien Dart, <damiendart@pobox.com>.
// This file is distributed under the MIT licence. For more information,
// please refer to the accompanying "LICENCE" file.

declare(strict_types=1);

namespace Yassg\Tests\Unit\Events;

use PHPUnit\Framework\TestCase;
use Yassg\Events\Event;
use Yassg\Events\FileEventInterface;
use Yassg\Events\FileWrittenEvent;

/**
 * @covers \Yassg\Events\FileWrittenEvent
 *
 * @internal
 */
class FileWrittenEventTest extends TestCase
{
    public function testRetrievingPathnames(): void
    {
        $event = new FileWrittenEvent(
            __FILE__,
            basename(__FILE__),
            __DIR__,
        );

        $this->assertInstanceOf(Event::class, $event);
        $this->assertInstanceOf(FileEventInterface::class, $event);

        $this->assertEquals(
            __FILE__,
            $event->getInputAbsolutePathname(),
        );

        $this->assertEquals(
            __FILE__,
            $event->getOutputAbsolutePathname(),
        );
    }
}
