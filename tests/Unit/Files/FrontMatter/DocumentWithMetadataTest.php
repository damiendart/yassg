<?php

/*
 * Copyright (c) 2022 Damien Dart, <damiendart@pobox.com>.
 * This file is distributed under the MIT licence. For more information,
 * please refer to the accompanying "LICENCE" file.
 */

declare(strict_types=1);

namespace Yassg\Tests\Unit\Files\FrontMatter;

use PHPUnit\Framework\TestCase;
use Yassg\Files\FrontMatter\DocumentWithMetadata;

/**
 * @covers \Yassg\Files\FrontMatter\DocumentWithMetadata
 *
 * @internal
 */
class DocumentWithMetadataTest extends TestCase
{
    public function testRetrievingContentAndMetadata(): void
    {
        $document = new DocumentWithMetadata(
            ['foo' => 'bar'],
            'This is a test!',
        );

        $this->assertSame(['foo' => 'bar'], $document->getMetadata());
        $this->assertSame('This is a test!', $document->getContent());
    }
}
