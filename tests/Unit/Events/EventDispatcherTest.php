<?php

/*
 * Copyright (c) 2022 Damien Dart, <damiendart@pobox.com>.
 * This file is distributed under the MIT licence. For more information,
 * please refer to the accompanying "LICENCE" file.
 */

declare(strict_types=1);

namespace Yassg\Tests\Unit\Events;

use PHPUnit\Framework\TestCase;
use Yassg\Events\AbstractEvent;
use Yassg\Events\EventDispatcher;

/**
 * @covers \Yassg\Events\EventDispatcher
 *
 * @internal
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

        $this->assertSame($listener->getEvent(), $event);
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

        $this->assertSame($listenerOne->getEvent(), $event);
        $this->assertSame($listenerTwo->getEvent(), $event);
    }

    public function testDispatchingAnEventToTheCorrectListener(): void
    {
        $dispatcher = new EventDispatcher();
        $eventOne = new TestEvent();
        $eventTwo = new TestEventTheSecond();
        $listenerOne = new TestEventListener();
        $listenerTwo = new TestEventListener();

        $dispatcher->addEventListener($eventOne::class, $listenerOne);
        $dispatcher->addEventListener($eventTwo::class, $listenerTwo);
        $dispatcher->dispatch($eventOne);
        $dispatcher->dispatch($eventTwo);

        $this->assertSame($listenerOne->getEvent(), $eventOne);
        $this->assertSame($listenerTwo->getEvent(), $eventTwo);
    }

    public function testDispatchingAnEventShouldReturnIt(): void
    {
        $dispatcher = new EventDispatcher();
        $event = new \stdClass();

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
        $eventOne = new TestEvent();
        $eventTwo = new TestEventTheSecond();
        $listener = new TestEventListener();

        $dispatcher->addEventListener($eventOne::class, $listener);
        $dispatcher->addEventListener($eventTwo::class, $listener);
        $dispatcher->dispatch($eventOne);
        $dispatcher->removeEventListeners($eventTwo::class);
        $dispatcher->dispatch($eventTwo);

        $this->assertSame($listener->getEvent(), $eventOne);
    }

    public function testRegisteringAListenerToMultipleEvents(): void
    {
        $dispatcher = new EventDispatcher();
        $eventOne = new TestEvent();
        $eventTwo = new TestEventTheSecond();
        $listener = new TestEventListener();

        $dispatcher->addEventListener(
            [$eventOne::class, $eventTwo::class],
            $listener,
        );

        $dispatcher->dispatch($eventOne);
        $this->assertSame($listener->getEvent(), $eventOne);

        $dispatcher->dispatch($eventTwo);
        $this->assertSame($listener->getEvent(), $eventTwo);
    }
}

class TestEvent extends AbstractEvent {}

class TestEventTheSecond extends AbstractEvent {}

class TestEventListener
{
    private null|object $event = null;

    public function __invoke(object $event): void
    {
        $this->event = $event;
    }

    public function getEvent(): ?object
    {
        return $this->event;
    }
}
