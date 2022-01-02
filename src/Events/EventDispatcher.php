<?php

// Copyright (c) 2022 Damien Dart, <damiendart@pobox.com>.
// This file is distributed under the MIT licence. For more information,
// please refer to the accompanying "LICENCE" file.

declare(strict_types=1);

namespace Yassg\Events;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\EventDispatcher\StoppableEventInterface;

class EventDispatcher implements EventDispatcherInterface, ListenerProviderInterface
{
    /** @var array<string, callable[]> */
    private array $listeners = [];

    public function addEventListener(
        string $eventClass,
        callable $listener,
    ): self {
        $this->listeners[$eventClass][] = $listener;

        return $this;
    }

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

        if (array_key_exists($eventType, $this->listeners)) {
            return $this->listeners[$eventType];
        }

        return [];
    }

    public function removeEventListeners(string $eventClass): void
    {
        if (array_key_exists($eventClass, $this->listeners)) {
            unset($this->listeners[$eventClass]);
        }
    }

    private function getEventType(object $event): string
    {
        return get_class($event);
    }
}
