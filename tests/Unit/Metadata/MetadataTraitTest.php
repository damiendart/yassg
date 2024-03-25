<?php

/*
 * Copyright (C) Damien Dart, <damiendart@pobox.com>.
 * This file is distributed under the MIT licence. For more information,
 * please refer to the accompanying "LICENCE" file.
 */

declare(strict_types=1);

namespace Yassg\Tests\Unit\Metadata;

use PHPUnit\Framework\TestCase;
use Yassg\Metadata\MetadataTrait;

/**
 * @covers \Yassg\Metadata\MetadataTrait
 *
 * @internal
 */
class MetadataTraitTest extends TestCase
{
    public function testCreatingMetadata(): void
    {
        $metadata = new class () {
            use MetadataTrait;
        };

        $this->assertSame([], $metadata->getMetadata());
    }

    public function testSettingMetadata(): void
    {
        $metadata = new class () {
            use MetadataTrait;
        };

        $metadata->setMetadata(['hello' => 'world']);
        $this->assertSame(['hello' => 'world'], $metadata->getMetadata());

        $metadata->setMetadata(['dog' => 'bark']);
        $this->assertSame(['dog' => 'bark'], $metadata->getMetadata());
    }

    public function testMergingMetadata(): void
    {
        $metadata = new class () {
            use MetadataTrait;
        };

        $metadata->setMetadata(['hello' => 'world']);
        $metadata->mergeMetadata(['dog' => 'bark']);
        $this->assertSame(
            ['hello' => 'world', 'dog' => 'bark'],
            $metadata->getMetadata(),
        );
    }
}
