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

use function Yassg\fopen_safe;
use function Yassg\Tests\stream_get_contents_safe;

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
            fopen_safe('php://memory', 'a'),
            $stdout = fopen_safe('php://memory', 'a'),
        );

        $helpCommand->run($output);

        rewind($stdout);
        $outputContent = stream_get_contents_safe($stdout);

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
            fopen_safe('php://memory', 'a'),
            $stdout = fopen_safe('php://memory', 'a'),
        );

        $output->setVerbosity(OutputInterface::VERBOSITY_VERBOSE);
        $helpCommand->run($output);

        rewind($stdout);
        $outputContent = stream_get_contents_safe($stdout);

        $this->assertStringEndsWith("\n\n", $outputContent);
    }
}
