<?php

/*
 * Copyright (c) 2022 Damien Dart, <damiendart@pobox.com>.
 * This file is distributed under the MIT licence. For more information,
 * please refer to the accompanying "LICENCE" file.
 */

declare(strict_types=1);

namespace Yassg\Tests\Unit\Files;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Yassg\Files\WriteFile;

/**
 * @covers \Yassg\Files\WriteFile
 *
 * @internal
 */
class WriteFileTest extends TestCase
{
    public function testGettingRelativePathname(): void
    {
        $writeFile = new WriteFile('This is content.', basename(__FILE__));

        $this->assertEquals(
            basename(__FILE__),
            $writeFile->getRelativePathname(),
        );
    }

    public function testWritingAFile(): void
    {
        $filesystem = $this
            ->getMockBuilder(Filesystem::class)
            ->disableOriginalConstructor()
            ->getMock();
        $writeFile = new WriteFile('This is content.', basename(__FILE__));

        $filesystem->expects(self::once())
            ->method('dumpFile')
            ->with(__FILE__, 'This is content.');

        $writeFile->write($filesystem, __DIR__);
    }
}
