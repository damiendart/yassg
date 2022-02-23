<?php

// Copyright (c) 2022 Damien Dart, <damiendart@pobox.com>.
// This file is distributed under the MIT licence. For more information,
// please refer to the accompanying "LICENCE" file.

declare(strict_types=1);

namespace Yassg\Tests\Unit\Application;

use PHPUnit\Framework\TestCase;
use Yassg\Application\ConsoleOutput;
use Yassg\Application\OutputInterface;

/**
 * @covers \Yassg\Application\ConsoleOutput
 *
 * @internal
 */
class ConsoleOutputTest extends TestCase
{
    /** @var resource */
    private $errorStream;

    /** @var resource */
    private $standardStream;

    public function setUp(): void
    {
        $this->errorStream = fopen('php://memory', 'a');
        $this->standardStream = fopen('php://memory', 'a');
    }

    public function testWritingToStreams(): void
    {
        $output = new ConsoleOutput(
            $this->errorStream,
            $this->standardStream,
        );

        $output
            ->write('This is a test!')
            ->writeError('This is another test!');

        rewind($this->standardStream);
        rewind($this->errorStream);

        $this->assertEquals(
            'This is a test!',
            stream_get_contents($this->standardStream),
        );
        $this->assertEquals(
            'This is another test!',
            stream_get_contents($this->errorStream),
        );
    }

    public function testSettingVerbosity(): void
    {
        $output = new ConsoleOutput(
            $this->errorStream,
            $this->standardStream,
        );

        $this->assertFalse($output->isVerbose());

        $output->setVerbosity(OutputInterface::VERBOSITY_VERBOSE);

        $this->assertTrue($output->isVerbose());
    }
}
