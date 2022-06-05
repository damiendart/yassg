<?php

/*
 * Copyright (c) 2022 Damien Dart, <damiendart@pobox.com>.
 * This file is distributed under the MIT licence. For more information,
 * please refer to the accompanying "LICENCE" file.
 */

declare(strict_types=1);

namespace Yassg\Tests\Unit\Services\Slug;

use PHPUnit\Framework\TestCase;
use Yassg\Services\Slug\SlugService;
use Yassg\Services\Slug\SlugStrategyInterface;

/**
 * @covers \Yassg\Services\Slug\SlugService
 *
 * @internal
 */
class SlugServiceTest extends TestCase
{
    public function testCallingSlugifyWillUseTheSetStrategy(): void
    {
        $mock = $this->createMock(SlugStrategyInterface::class);

        $mock->expects($this->once())->method('slugify')->with('test');

        $service = new SlugService($mock);

        $service->slugify('test');
    }
}
