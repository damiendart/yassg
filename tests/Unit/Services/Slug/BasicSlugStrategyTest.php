<?php

// Copyright (c) 2022 Damien Dart, <damiendart@pobox.com>.
// This file is distributed under the MIT licence. For more information,
// please refer to the accompanying "LICENCE" file.

declare(strict_types=1);

namespace Yassg\Tests\Unit\Services\Slug;

use PHPUnit\Framework\TestCase;
use Yassg\Services\Slug\BasicSlugStrategy;

/**
 * @internal
 * @coversNothing
 */
class BasicSlugStrategyTest extends TestCase
{
    /** @dataProvider slugifyTestStringProvider */
    public function testSlugifyAString(string $input, string $expected): void
    {
        $this->assertEquals(
            $expected,
            (new BasicSlugStrategy())->slugify($input),
        );
    }

    public function slugifyTestStringProvider(): array
    {
        return [
            ['index.html', '/'],
            ['/index.html', '/'],
            ['/index.html.twig', '/'],
            ['test/index.html', '/test/'],
            ['/test/index.html', '/test/'],
            ['/test/index.html.twig', '/test/'],
            ['test/test.html', '/test/test'],
            ['/test/test.html', '/test/test'],
            ['index.htm', '/'],
            ['/index.htm', '/'],
            ['test/index.htm', '/test/'],
            ['/test/index.htm', '/test/'],
            ['test/test.htm', '/test/test'],
            ['/test/test.htm', '/test/test'],
            ['index.php', '/'],
            ['/index.php', '/'],
            ['test/index.php', '/test/'],
            ['/test/index.php', '/test/'],
            ['/test/index.php.twig', '/test/'],
            ['test/test.php', '/test/test'],
            ['/test/test.php', '/test/test'],
            ['/test/test.php.twig', '/test/test'],
            ['index.pdf', '/index.pdf'],
            ['index.pdf', '/index.pdf'],
            ['test/index.pdf', '/test/index.pdf'],
            ['/test/index.pdf', '/test/index.pdf'],
        ];
    }
}
