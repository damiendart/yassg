<?php

/*
 * Copyright (c) 2022 Damien Dart, <damiendart@pobox.com>.
 * This file is distributed under the MIT licence. For more information,
 * please refer to the accompanying "LICENCE" file.
 */

declare(strict_types=1);

namespace Yassg\Tests\Unit\Plugins\HelloWorld;

use PHPUnit\Framework\TestCase;
use Yassg\Files\InputFile;
use Yassg\Files\Metadata\MetadataExtractor;
use Yassg\Plugins\HelloWorld\HelloWorldMetadataExtractor;

/**
 * @covers \Yassg\Plugins\HelloWorld\HelloWorldMetadataExtractor
 *
 * @internal
 */
class HelloWorldMetadataExtractorTest extends TestCase
{
    public function testAddingMetadataToInputFiles(): void
    {
        $baseExtractor = $this
            ->getMockBuilder(MetadataExtractor::class)
            ->getMock();
        $extractor = new HelloWorldMetadataExtractor($baseExtractor);
        $inputFile = new InputFile(__FILE__, basename(__FILE__));

        $extractor->addMetadata($inputFile);

        $this->assertSame(
            'Hello, World!',
            $inputFile->getMetadata()['hello'],
        );
    }

    public function testCallingInnerExtractor(): void
    {
        $baseExtractor = $this
            ->getMockBuilder(MetadataExtractor::class)
            ->getMock();
        $extractor = new HelloWorldMetadataExtractor($baseExtractor);
        $inputFile = new InputFile(__FILE__, basename(__FILE__));

        $baseExtractor
            ->expects(self::once())
            ->method('addMetadata')
            ->with($inputFile);

        $extractor->addMetadata($inputFile);
    }
}
