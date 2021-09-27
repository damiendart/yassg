<?php

// Copyright (C) 2021 Damien Dart, <damiendart@pobox.com>.
// This file is distributed under the MIT licence. For more information,
// please refer to the accompanying "LICENCE" file.

declare(strict_types=1);

namespace Yassg\Tests\Functional\Commands;

use DI\ContainerBuilder;
use Symfony\Component\Console\Command\Command;
use Yassg\Commands\BuildCommand;
use Yassg\Configuration\Configuration;

/**
 * @internal
 * @coversNothing
 */
class BuildCommandTest extends CommandTestBase
{
    public Command $buildCommand;

    public function setUp(): void
    {
        $this->buildCommand = (new ContainerBuilder())
            ->build()
            ->get(BuildCommand::class);

        parent::setUp();
    }

    public function tearDown(): void
    {
        unset($this->buildCommand);

        parent::tearDown();
    }

    public function testRunningTheBuildCommandWithAnInvalidInputDirectory(): void
    {
        $fixtureDirectoryPath = self::$fixturesDirectoryPath
            . DIRECTORY_SEPARATOR
            . 'invalid-input-directory';

        /** @var Configuration $fixtureConfiguration */
        $fixtureConfiguration = include $fixtureDirectoryPath
            . DIRECTORY_SEPARATOR
            . '.yassg.php';

        $this->setTemporaryDirectoryPath(
            $fixtureConfiguration->getOutputDirectory(),
        );

        $commandTester = $this->runCommand(
            $this->buildCommand,
            $fixtureDirectoryPath,
        );

        $this->assertEquals(
            Command::FAILURE,
            $commandTester->getStatusCode(),
        );
        $this->assertDirectoryDoesNotExist(
            $fixtureConfiguration->getOutputDirectory(),
        );
    }

    public function testRunningTheBuildCommandWithADirectoryOfTextFiles(): void
    {
        $fixtureDirectoryPath = self::$fixturesDirectoryPath
            . DIRECTORY_SEPARATOR
            . 'just-text-files';

        /** @var Configuration $fixtureConfiguration */
        $fixtureConfiguration = include $fixtureDirectoryPath
            . DIRECTORY_SEPARATOR
            . '.yassg.php';

        $this->setTemporaryDirectoryPath(
            $fixtureConfiguration->getOutputDirectory(),
        );

        $commandTester = $this->runCommand(
            $this->buildCommand,
            $fixtureDirectoryPath,
        );

        $this->assertEquals(
            Command::SUCCESS,
            $commandTester->getStatusCode(),
        );
        $this->assertDirectoryEquals(
            $fixtureConfiguration->getInputDirectory(),
            $fixtureConfiguration->getOutputDirectory(),
        );
    }
}
