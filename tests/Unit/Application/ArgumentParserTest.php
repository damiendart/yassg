<?php

// Copyright (c) 2022 Damien Dart, <damiendart@pobox.com>.
// This file is distributed under the MIT licence. For more information,
// please refer to the accompanying "LICENCE" file.

declare(strict_types=1);

namespace Yassg\Tests\Unit\Application;

use PHPUnit\Framework\TestCase;
use Yassg\Application\ArgumentParser;
use Yassg\Exceptions\InvalidArgumentException;

/**
 * @covers \Yassg\Application\ArgumentParser
 *
 * @internal
 */
class ArgumentParserTest extends TestCase
{
    public function testParsingAnEmptyArray(): void
    {
        $parser = new ArgumentParser([]);

        $this->assertNull($parser->getConfigurationFilePathname());
        $this->assertFalse($parser->isHelpFlagSet());
        $this->assertFalse($parser->isVerboseFlagSet());
    }

    public function testParsingTheConfigOption(): void
    {
        foreach (['-c', '--config'] as $option) {
            $parser = new ArgumentParser(['yassg', $option, './dummy']);

            $this->assertSame(
                './dummy',
                $parser->getConfigurationFilePathname(),
            );
            $this->assertFalse($parser->isHelpFlagSet());
            $this->assertFalse($parser->isVerboseFlagSet());
        }

        foreach (['-c=./dummy=file', '--config=./dummy=file'] as $option) {
            $parser = new ArgumentParser(['yassg', $option]);

            $this->assertSame(
                './dummy=file',
                $parser->getConfigurationFilePathname(),
            );
            $this->assertFalse($parser->isHelpFlagSet());
            $this->assertFalse($parser->isVerboseFlagSet());
        }
    }

    public function testParsingTheHelpOption(): void
    {
        foreach (['-h', '--help'] as $option) {
            $parser = new ArgumentParser(['yassg', $option]);

            $this->assertNull($parser->getConfigurationFilePathname());
            $this->assertTrue($parser->isHelpFlagSet());
            $this->assertFalse($parser->isVerboseFlagSet());
        }
    }

    public function testParsingTheVerboseOption(): void
    {
        $parser = new ArgumentParser(['yassg', '--verbose']);

        $this->assertNull($parser->getConfigurationFilePathname());
        $this->assertFalse($parser->isHelpFlagSet());
        $this->assertTrue($parser->isVerboseFlagSet());
    }

    public function testParsingMultipleOptions(): void
    {
        $parser = new ArgumentParser(['yassg', '--help', '--verbose']);

        $this->assertNull($parser->getConfigurationFilePathname());
        $this->assertTrue($parser->isHelpFlagSet());
        $this->assertTrue($parser->isVerboseFlagSet());

        $parser = new ArgumentParser(
            ['yassg', '--verbose', '--config', './test'],
        );

        $this->assertSame('./test', $parser->getConfigurationFilePathname());
        $this->assertFalse($parser->isHelpFlagSet());
        $this->assertTrue($parser->isVerboseFlagSet());

        $parser = new ArgumentParser(
            ['yassg', '--help', '--config', './test'],
        );

        $this->assertNull($parser->getConfigurationFilePathname());
        $this->assertTrue($parser->isHelpFlagSet());
        $this->assertFalse($parser->isVerboseFlagSet());
    }

    /** @dataProvider invalidArgumentsProvider */
    public function testParsingInvalidArguments(
        string $expectedErrorMessage,
        array $input,
    ): void {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($expectedErrorMessage);

        new ArgumentParser($input);
    }

    public function invalidArgumentsProvider(): array
    {
        return [
            'invalid short option' => [
                'Invalid argument or option: "-n".',
                ['yassg', '-n'],
            ],
            'invalid long option' => [
                'Invalid argument or option: "--nope".',
                ['yassg', '--nope'],
            ],
            'missing value' => [
                'Missing value for "--config".',
                ['yassg', '--config'],
            ],
            'positional argument' => [
                'Invalid argument or option: "meow".',
                ['yassg', '--help', 'meow'],
            ],
            'end of option list doohickey' => [
                'Positional command-line arguments are not accepted.',
                ['yassg', '--help', '--', 'meow', 'woof'],
            ],
        ];
    }
}
