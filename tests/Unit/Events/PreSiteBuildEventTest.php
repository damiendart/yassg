<?php

/*
 * Copyright (c) 2022 Damien Dart, <damiendart@pobox.com>.
 * This file is distributed under the MIT licence. For more information,
 * please refer to the accompanying "LICENCE" file.
 */

declare(strict_types=1);

namespace Yassg\Tests\Unit\Events;

use PHPUnit\Framework\TestCase;
use Yassg\Events\PreSiteBuildEvent;
use Yassg\Files\InputFileCollection;

/**
 * @covers \Yassg\Events\PreSiteBuildEvent
 *
 * @internal
 */
class PreSiteBuildEventTest extends TestCase
{
    public function testRetrievingCollection(): void
    {
        $inputFiles = new InputFileCollection();
        $event = new PreSiteBuildEvent($inputFiles);

        $this->assertSame($inputFiles, $event->getInputFiles());
    }
}
