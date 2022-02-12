<?php

// Copyright (c) 2022 Damien Dart, <damiendart@pobox.com>.
// This file is distributed under the MIT licence. For more information,
// please refer to the accompanying "LICENCE" file.

declare(strict_types=1);

namespace Yassg\Tests\Unit\Configuration;

use PHPUnit\Framework\TestCase;
use Yassg\Configuration\Configuration;
use Yassg\Plugins\HelloWorld\HelloWorldPlugin;
use Yassg\Plugins\Slug\SlugPlugin;
use Yassg\Services\Slug\BasicSlugStrategy;

/**
 * @covers \Yassg\Configuration\Configuration
 *
 * @internal
 */
class ConfigurationTest extends TestCase
{
    public function testSettingInputAndOutputDirectories(): void
    {
        $configuration = new Configuration(
            __DIR__ . DIRECTORY_SEPARATOR . 'input',
            __DIR__ . DIRECTORY_SEPARATOR . 'output',
        );

        $this->assertEquals(
            __DIR__ . DIRECTORY_SEPARATOR . 'input',
            $configuration->getInputDirectory(),
        );

        $this->assertEquals(
            __DIR__ . DIRECTORY_SEPARATOR . 'output',
            $configuration->getOutputDirectory(),
        );
    }

    public function testAddingPlugins(): void
    {
        $configuration = new Configuration(
            __DIR__ . DIRECTORY_SEPARATOR . 'input',
            __DIR__ . DIRECTORY_SEPARATOR . 'output',
        );
        $helloWorldPlugin = new HelloWorldPlugin();
        $slugPlugin = new SlugPlugin(new BasicSlugStrategy());

        $configuration->addPlugin($helloWorldPlugin);
        $configuration->addPlugin($slugPlugin);

        $this->assertSame($helloWorldPlugin, $configuration->getPlugins()[0]);
        $this->assertSame($slugPlugin, $configuration->getPlugins()[1]);
    }

    public function testSettingGlobalMetadata(): void
    {
        $configuration = new Configuration(
            __DIR__ . DIRECTORY_SEPARATOR . 'input',
            __DIR__ . DIRECTORY_SEPARATOR . 'output',
        );

        $this->assertEquals([], $configuration->getMetadata());

        $configuration->setMetadata(['hello' => 'world']);

        $this->assertEquals(
            ['hello' => 'world'],
            $configuration->getMetadata(),
        );

        $configuration->setMetadata(['hello' => 'you']);

        $this->assertEquals(
            ['hello' => 'you'],
            $configuration->getMetadata(),
        );
    }
}
