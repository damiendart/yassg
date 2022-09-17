<?php

/*
 * Copyright (c) 2022 Damien Dart, <damiendart@pobox.com>.
 * This file is distributed under the MIT licence. For more information,
 * please refer to the accompanying "LICENCE" file.
 */

declare(strict_types=1);

namespace Yassg\Tests\Unit\Plugins\Slug;

use PHPUnit\Framework\TestCase;
use Yassg\Files\InputFile;
use Yassg\Files\Metadata\MetadataExtractor;
use Yassg\Plugins\Slug\BasicSlugStrategy;
use Yassg\Plugins\Slug\SlugMetadataExtractor;

/**
 * @covers \Yassg\Plugins\Slug\SlugMetadataExtractor
 *
 * @internal
 */
class SlugMetadataExtractorTest extends TestCase
{
    public function testAddingSlugMetadataToInputFiles(): void
    {
        $baseExtractor = $this
            ->getMockBuilder(MetadataExtractor::class)
            ->getMock();
        $extractor = new SlugMetadataExtractor(
            new BasicSlugStrategy(),
            $baseExtractor,
        );
        $inputFile = new InputFile(__FILE__, basename(__FILE__));

        $extractor->addMetadata($inputFile);

        $this->assertSame(
            basename(__FILE__, '.php'),
            $inputFile->getMetadata()['slug'],
        );
    }

    public function testSkippingAddingSlugMetadataToInputFiles(): void
    {
        $baseExtractor = $this
            ->getMockBuilder(MetadataExtractor::class)
            ->getMock();
        $extractor = new SlugMetadataExtractor(
            new BasicSlugStrategy(),
            $baseExtractor,
        );
        $inputFile = new InputFile(__FILE__, basename(__FILE__));

        $inputFile->mergeMetadata(['slug' => 'blah']);
        $extractor->addMetadata($inputFile);

        $this->assertSame('blah', $inputFile->getMetadata()['slug']);
    }

    public function testCallingInnerExtractor(): void
    {
        $baseExtractor = $this
            ->getMockBuilder(MetadataExtractor::class)
            ->getMock();
        $extractor = new SlugMetadataExtractor(
            new BasicSlugStrategy(),
            $baseExtractor,
        );
        $inputFile = new InputFile(__FILE__, basename(__FILE__));

        $baseExtractor
            ->expects(self::once())
            ->method('addMetadata')
            ->with($inputFile);

        $extractor->addMetadata($inputFile);
    }
}
