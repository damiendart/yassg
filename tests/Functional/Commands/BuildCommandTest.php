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

        $command = (new ContainerBuilder())
            ->addDefinitions([Configuration::class => $fixtureConfiguration])
            ->build()
            ->get(BuildCommand::class);

        $commandTester = $this->runCommand($command, $fixtureDirectoryPath);

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

        $command = (new ContainerBuilder())
            ->addDefinitions([Configuration::class => $fixtureConfiguration])
            ->build()
            ->get(BuildCommand::class);

        $commandTester = $this->runCommand($command, $fixtureDirectoryPath);

        $this->assertEquals(
            Command::SUCCESS,
            $commandTester->getStatusCode(),
        );
        $this->assertDirectoryEquals(
            $fixtureConfiguration->getInputDirectory(),
            $fixtureConfiguration->getOutputDirectory(),
        );
    }

    public function testRunningTheBuildCommandWithADirectoryOfMarkdownFiles(): void
    {
        $fixtureDirectoryPath = self::$fixturesDirectoryPath
            . DIRECTORY_SEPARATOR
            . 'just-markdown-files';

        /** @var Configuration $fixtureConfiguration */
        $fixtureConfiguration = include $fixtureDirectoryPath
            . DIRECTORY_SEPARATOR
            . '.yassg.php';

        $this->setTemporaryDirectoryPath(
            $fixtureConfiguration->getOutputDirectory(),
        );

        $command = (new ContainerBuilder())
            ->addDefinitions([Configuration::class => $fixtureConfiguration])
            ->build()
            ->get(BuildCommand::class);

        $commandTester = $this->runCommand($command, $fixtureDirectoryPath);

        $this->assertEquals(
            Command::SUCCESS,
            $commandTester->getStatusCode(),
        );
        $this->assertDirectoryEquals(
            $fixtureDirectoryPath
            . DIRECTORY_SEPARATOR
            . 'expected',
            $fixtureConfiguration->getOutputDirectory(),
        );
    }

    public function testRunningTheBuildCommandWithTheConfigOption(): void
    {
        $fixtureDirectoryPath = self::$fixturesDirectoryPath
            . DIRECTORY_SEPARATOR
            . 'non-standard-configuration-filename';

        /** @var Configuration $fixtureConfiguration */
        $fixtureConfiguration = include $fixtureDirectoryPath
            . DIRECTORY_SEPARATOR
            . '.chicken.php';

        $this->setTemporaryDirectoryPath(
            $fixtureConfiguration->getOutputDirectory(),
        );

        $command = (new ContainerBuilder())
            ->addDefinitions([Configuration::class => $fixtureConfiguration])
            ->build()
            ->get(BuildCommand::class);

        $commandTester = $this->runCommand($command, $fixtureDirectoryPath);

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
