<?php

/*
 * Copyright (C) Damien Dart, <damiendart@pobox.com>.
 * This file is distributed under the MIT licence. For more information,
 * please refer to the accompanying "LICENCE" file.
 */

declare(strict_types=1);

namespace Yassg\Tests\Unit\Files;

use PHPUnit\Framework\TestCase;
use Yassg\Files\InputFile;
use Yassg\Files\InputFileCollection;

/**
 * @covers \Yassg\Files\InputFileCollection
 *
 * @internal
 */
class InputFileCollectionTest extends TestCase
{
    public function testTraversableCapabilities(): void
    {
        $collection = new InputFileCollection();

        $this->assertInstanceOf(\Traversable::class, $collection);
    }

    public function testGetIterator(): void
    {
        $collection = new InputFileCollection();
        $inputFiles = [];

        foreach (range(0, 4) as $i) {
            $inputFiles[] = new InputFile(
                __FILE__ . ".{$i}",
                __FILE__ . ".{$i}",
            );

            $collection->addInputFile($inputFiles[$i]);
        }

        $this->assertInstanceOf(
            \Iterator::class,
            $collection->getIterator(),
        );
        $this->assertEquals(
            $inputFiles,
            $collection->getIterator()->getArrayCopy(),
        );
    }
}
