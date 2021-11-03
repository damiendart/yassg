<?php

// Copyright (C) 2021 Damien Dart, <damiendart@pobox.com>.
// This file is distributed under the MIT licence. For more information,
// please refer to the accompanying "LICENCE" file.

declare(strict_types=1);

namespace Yassg\Tests\Functional\Commands;

use Symfony\Component\Console\Command\Command;
use Yassg\Commands\BuildCommand;
use Yassg\Configuration\Configuration;
use Yassg\Container\Container;

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
        $fixtureConfigurationFilepath = $fixtureDirectoryPath
            . DIRECTORY_SEPARATOR
            . '.yassg.php';

        /** @var Configuration $configuration */
        $configuration = include $fixtureConfigurationFilepath;

        $this->setTemporaryDirectoryPath(
            $configuration->getOutputDirectory(),
        );

        $command = (new Container($fixtureConfigurationFilepath))
            ->get(BuildCommand::class);

        $commandTester = $this->runCommand($command, $fixtureDirectoryPath);

        $this->assertEquals(
            Command::FAILURE,
            $commandTester->getStatusCode(),
        );
        $this->assertDirectoryDoesNotExist(
            $configuration->getOutputDirectory(),
        );
    }

    public function testRunningTheBuildCommandWithADirectoryOfTextFiles(): void
    {
        $fixtureDirectoryPath = self::$fixturesDirectoryPath
            . DIRECTORY_SEPARATOR
            . 'just-text-files';
        $fixtureConfigurationFilepath = $fixtureDirectoryPath
            . DIRECTORY_SEPARATOR
            . '.yassg.php';

        /** @var Configuration $configuration */
        $configuration = include $fixtureConfigurationFilepath;

        $this->setTemporaryDirectoryPath(
            $configuration->getOutputDirectory(),
        );

        $command = (new Container($fixtureConfigurationFilepath))
            ->get(BuildCommand::class);

        $commandTester = $this->runCommand($command, $fixtureDirectoryPath);

        $this->assertEquals(
            Command::SUCCESS,
            $commandTester->getStatusCode(),
        );
        $this->assertDirectoryEquals(
            $configuration->getInputDirectory(),
            $configuration->getOutputDirectory(),
        );
    }

    public function testRunningTheBuildCommandWithADirectoryOfMarkdownFiles(): void
    {
        $fixtureDirectoryPath = self::$fixturesDirectoryPath
            . DIRECTORY_SEPARATOR
            . 'just-markdown-files';
        $fixtureConfigurationFilepath = $fixtureDirectoryPath
            . DIRECTORY_SEPARATOR
            . '.yassg.php';

        /** @var Configuration $configuration */
        $configuration = include $fixtureConfigurationFilepath;

        $this->setTemporaryDirectoryPath(
            $configuration->getOutputDirectory(),
        );

        $command = (new Container($fixtureConfigurationFilepath))
            ->get(BuildCommand::class);

        $commandTester = $this->runCommand($command, $fixtureDirectoryPath);

        $this->assertEquals(
            Command::SUCCESS,
            $commandTester->getStatusCode(),
        );
        $this->assertDirectoryEquals(
            $fixtureDirectoryPath . DIRECTORY_SEPARATOR . 'expected',
            $configuration->getOutputDirectory(),
        );
    }
}
