<?php

/*
 * Copyright (c) 2022 Damien Dart, <damiendart@pobox.com>.
 * This file is distributed under the MIT licence. For more information,
 * please refer to the accompanying "LICENCE" file.
 */

declare(strict_types=1);

namespace Yassg\Events;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\EventDispatcher\StoppableEventInterface;

class EventDispatcher implements EventDispatcherInterface, ListenerProviderInterface
{
    /** @var array<class-string, callable[]> */
    private array $listeners = [];

    /** @param class-string|class-string[] $eventClasses */
    public function addEventListener(
        array|string $eventClasses,
        callable $listener,
    ): self {
        if (\is_string($eventClasses)) {
            $eventClasses = [$eventClasses];
        }

        foreach ($eventClasses as $eventClass) {
            $this->listeners[$eventClass][] = $listener;
        }

        return $this;
    }

    /**
     * @psalm-api
     */
    public function dispatch(object $event): object
    {
        foreach (
            $this->getListenersForEvent($event) as $listener
        ) {
            if (
                $event instanceof StoppableEventInterface
                && $event->isPropagationStopped()
            ) {
                break;
            }

            $listener($event);
        }

        return $event;
    }

    /**
     * @return callable[]
     */
    public function getListenersForEvent(object $event): iterable
    {
        $eventType = $this->getEventType($event);

        if (\array_key_exists($eventType, $this->listeners)) {
            return $this->listeners[$eventType];
        }

        return [];
    }

    /**
     * @param class-string $eventClass
     *
     * @psalm-api
     */
    public function removeEventListeners(string $eventClass): void
    {
        if (\array_key_exists($eventClass, $this->listeners)) {
            unset($this->listeners[$eventClass]);
        }
    }

    /** @return class-string */
    private function getEventType(object $event): string
    {
        return \get_class($event);
    }
}
