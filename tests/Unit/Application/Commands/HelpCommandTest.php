<?php

/*
 * Copyright (c) 2022 Damien Dart, <damiendart@pobox.com>.
 * This file is distributed under the MIT licence. For more information,
 * please refer to the accompanying "LICENCE" file.
 */

declare(strict_types=1);

namespace Yassg\Tests\Unit\Application\Commands;

use PHPUnit\Framework\TestCase;
use Yassg\Application\Commands\HelpCommand;
use Yassg\Application\ConsoleOutput;
use Yassg\Application\OutputInterface;

/**
 * @covers \Yassg\Application\Commands\HelpCommand
 *
 * @internal
 */
class HelpCommandTest extends TestCase
{
    public function testHelpTextOutput(): void
    {
        $helpCommand = new HelpCommand();
        $output = new ConsoleOutput(
            fopen('php://memory', 'a'),
            $stdout = fopen('php://memory', 'a'),
        );

        $helpCommand->run($output);

        rewind($stdout);
        $outputContent = stream_get_contents($stdout);

        $this->assertStringContainsString(
            '-c FILE, --config=FILE',
            $outputContent,
        );
        $this->assertStringContainsString('-h, --help', $outputContent);
        $this->assertStringContainsString('-v, --verbose', $outputContent);

        $this->assertDoesNotMatchRegularExpression(
            '/.{73,}/m',
            $outputContent,
        );
    }

    public function testVerboseHelpTextOutput(): void
    {
        $helpCommand = new HelpCommand();
        $output = new ConsoleOutput(
            fopen('php://memory', 'a'),
            $stdout = fopen('php://memory', 'a'),
        );

        $output->setVerbosity(OutputInterface::VERBOSITY_VERBOSE);
        $helpCommand->run($output);

        rewind($stdout);
        $outputContent = stream_get_contents($stdout);

        $this->assertStringEndsWith("\n\n", $outputContent);
    }
}
