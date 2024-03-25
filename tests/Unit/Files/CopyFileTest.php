<?php

/*
 * Copyright (C) Damien Dart, <damiendart@pobox.com>.
 * This file is distributed under the MIT licence. For more information,
 * please refer to the accompanying "LICENCE" file.
 */

declare(strict_types=1);

namespace Yassg\Tests\Unit\Files;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Yassg\Files\CopyFile;
use Yassg\Files\InputFile;

/**
 * @covers \Yassg\Files\CopyFile
 *
 * @internal
 */
class CopyFileTest extends TestCase
{
    public function testGettingRelativePathname(): void
    {
        $copyFile = new CopyFile(new InputFile(__FILE__, basename(__FILE__)));

        $this->assertEquals(
            basename(__FILE__),
            $copyFile->getRelativePathname(),
        );
    }

    public function testCopyingAFile(): void
    {
        $copyFile = new CopyFile(new InputFile(__FILE__, basename(__FILE__)));
        $filesystem = $this
            ->getMockBuilder(Filesystem::class)
            ->disableOriginalConstructor()
            ->getMock();

        $filesystem->expects(self::once())
            ->method('copy')
            ->with(__FILE__, __FILE__);

        $copyFile->write($filesystem, __DIR__);
    }
}
