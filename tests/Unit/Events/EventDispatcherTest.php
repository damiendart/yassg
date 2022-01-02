<?php

// Copyright (c) 2022 Damien Dart, <damiendart@pobox.com>.
// This file is distributed under the MIT licence. For more information,
// please refer to the accompanying "LICENCE" file.

declare(strict_types=1);

namespace Yassg\Tests\Unit\Events;

use PHPUnit\Framework\TestCase;
use stdClass;
use Yassg\Events\Event;
use Yassg\Events\EventDispatcher;

/**
 * @internal
 * @coversNothing
 */
class EventDispatcherTest extends TestCase
{
    public function testDispatchingAnEventToASingleListener(): void
    {
        $dispatcher = new EventDispatcher();
        $event = new TestEvent();
        $listener = new TestEventListener();

        $dispatcher->addEventListener($event::class, $listener);
        $dispatcher->dispatch($event);

        $this->assertEquals($listener->getEvent(), $event);
    }

    public function testDispatchingAnEventToMultipleListeners(): void
    {
        $dispatcher = new EventDispatcher();
        $event = new TestEvent();
        $listenerOne = new TestEventListener();
        $listenerTwo = new TestEventListener();

        $dispatcher->addEventListener($event::class, $listenerOne);
        $dispatcher->addEventListener($event::class, $listenerTwo);
        $dispatcher->dispatch($event);

        $this->assertEquals($listenerOne->getEvent(), $event);
        $this->assertEquals($listenerTwo->getEvent(), $event);
    }

    public function testDispatchingAnEventToTheCorrectListener(): void
    {
        $dispatcher = new EventDispatcher();
        $eventOne = new TestEvent();
        $eventTwo = new stdClass();
        $listenerOne = new TestEventListener();
        $listenerTwo = new TestEventListener();

        $dispatcher->addEventListener($eventOne::class, $listenerOne);
        $dispatcher->addEventListener($eventTwo::class, $listenerTwo);
        $dispatcher->dispatch($eventOne);
        $dispatcher->dispatch($eventTwo);

        $this->assertEquals($listenerOne->getEvent(), $eventOne);
        $this->assertEquals($listenerTwo->getEvent(), $eventTwo);
    }

    public function testDispatchingAnEventShouldReturnIt(): void
    {
        $dispatcher = new EventDispatcher();
        $event = new stdClass();

        $this->assertSame($dispatcher->dispatch($event), $event);
    }

    public function testStoppingEventPropagation(): void
    {
        $dispatcher = new EventDispatcher();
        $event = new TestEvent();
        $listener = new TestEventListener();

        $dispatcher->addEventListener(
            $event::class,
            function (TestEvent $event): void {
                $event->stopPropagation();
            },
        );
        $dispatcher->addEventListener($event::class, $listener);
        $dispatcher->dispatch($event);

        $this->assertEmpty($listener->getEvent());
    }

    public function testRemovingEventListeners(): void
    {
        $dispatcher = new EventDispatcher();
        $eventOne = new stdClass();
        $eventTwo = new TestEvent();
        $listener = new TestEventListener();

        $dispatcher->addEventListener($eventOne::class, $listener);
        $dispatcher->addEventListener($eventTwo::class, $listener);
        $dispatcher->dispatch($eventOne);
        $dispatcher->removeEventListeners($eventTwo::class);
        $dispatcher->dispatch($eventTwo);

        $this->assertEquals($listener->getEvent(), $eventOne);
    }
}

class TestEvent extends Event
{
}

class TestEventListener
{
    private object|null $event = null;

    public function __invoke(object $event): void
    {
        $this->event = $event;
    }

    public function getEvent(): ?object
    {
        return $this->event;
    }
}
