<?php

/*
 * Copyright (C) Damien Dart, <damiendart@pobox.com>.
 * This file is distributed under the MIT licence. For more information,
 * please refer to the accompanying "LICENCE" file.
 */

declare(strict_types=1);

namespace Yassg\Tests\Unit\Events;

use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Yassg\Events\TwigEnvironmentCreatedEvent;

/**
 * @covers \Yassg\Events\TwigEnvironmentCreatedEvent
 *
 * @internal
 */
class TwigEnvironmentCreatedEventTest extends TestCase
{
    public function testRetrievingEnvironment(): void
    {
        $environment = $this
            ->getMockBuilder(Environment::class)
            ->disableOriginalConstructor()
            ->getMock();
        $event = new TwigEnvironmentCreatedEvent($environment);

        $this->assertSame($environment, $event->getEnvironment());
    }
}
