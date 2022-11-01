<?php

/*
 * Copyright (c) 2022 Damien Dart, <damiendart@pobox.com>.
 * This file is distributed under the MIT licence. For more information,
 * please refer to the accompanying "LICENCE" file.
 */

declare(strict_types=1);

namespace Yassg\Tests\Unit\Files;

use PHPUnit\Framework\TestCase;

use function Yassg\file_get_contents_safe;

use Yassg\Files\InputFile;

/**
 * @covers \Yassg\Files\InputFile
 *
 * @internal
 */
class InputFileTest extends TestCase
{
    public function testRetrievingPathnames(): void
    {
        $inputFile = new InputFile(__FILE__, basename(__FILE__));

        $this->assertEquals(
            basename(__FILE__),
            $inputFile->getRelativePathname(),
        );
        $this->assertEquals(
            __FILE__,
            $inputFile->getOriginalAbsolutePathname(),
        );
    }

    public function testRetrievingAndSettingContent(): void
    {
        $inputFile = new InputFile(__FILE__, basename(__FILE__));

        $this->assertFalse($inputFile->isDirty());
        $this->assertEquals(
            file_get_contents_safe(__FILE__),
            $inputFile->getContent(),
        );

        $inputFile->setContent('This is a test!');

        $this->assertTrue($inputFile->isDirty());
        $this->assertEquals(
            'This is a test!',
            $inputFile->getContent(),
        );
    }

    public function testRetrievingTheOriginalInputFile(): void
    {
        $inputFile = new InputFile(__FILE__, basename(__FILE__));

        $this->assertSame(
            $inputFile,
            $inputFile->getOriginalInputFile(),
        );
    }

    public function testUsingPathnamesToNonExistentFiles(): void
    {
        $inputFile = new InputFile(
            __DIR__ . DIRECTORY_SEPARATOR . 'does-not-exist',
            'does-not-exist',
        );

        $this->expectException(\RuntimeException::class);
        $inputFile->getContent();
    }
}
