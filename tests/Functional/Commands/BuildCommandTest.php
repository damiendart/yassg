<?php

// Copyright (c) 2022 Damien Dart, <damiendart@pobox.com>.
// This file is distributed under the MIT licence. For more information,
// please refer to the accompanying "LICENCE" file.

declare(strict_types=1);

namespace Yassg\Tests\Functional\Commands;

use Symfony\Component\Console\Command\Command;
use Yassg\Application\Commands\BuildCommand;
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
        $fixtureDirectory = self::$fixturesDirectory
            . DIRECTORY_SEPARATOR
            . 'invalid-input-directory';
        $fixtureConfigurationFilePathname = $fixtureDirectory
            . DIRECTORY_SEPARATOR
            . '.yassg.php';

        /** @var Configuration $configuration */
        $configuration = include $fixtureConfigurationFilePathname;

        $this->setTemporaryDirectoryPath(
            $configuration->getOutputDirectory(),
        );

        $command = (new Container($fixtureConfigurationFilePathname))
            ->get(BuildCommand::class);

        $commandTester = $this->runCommand($command, $fixtureDirectory);

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
        $fixtureDirectory = self::$fixturesDirectory
            . DIRECTORY_SEPARATOR
            . 'just-text-files';
        $fixtureConfigurationFilePathname = $fixtureDirectory
            . DIRECTORY_SEPARATOR
            . '.yassg.php';

        /** @var Configuration $configuration */
        $configuration = include $fixtureConfigurationFilePathname;

        $this->setTemporaryDirectoryPath(
            $configuration->getOutputDirectory(),
        );

        $command = (new Container($fixtureConfigurationFilePathname))
            ->get(BuildCommand::class);

        $commandTester = $this->runCommand($command, $fixtureDirectory);

        $this->assertEquals(
            Command::SUCCESS,
            $commandTester->getStatusCode(),
        );
        $this->assertDirectoryEquals(
            $configuration->getInputDirectory(),
            $configuration->getOutputDirectory(),
        );
        $this->assertSummaryMatches(
            $configuration->getInputDirectory(),
            $commandTester,
        );
    }

    public function testRunningTheBuildCommandWithADirectoryOfMarkdownFiles(): void
    {
        $fixtureDirectory = self::$fixturesDirectory
            . DIRECTORY_SEPARATOR
            . 'just-markdown-files';
        $fixtureConfigurationFilePathname = $fixtureDirectory
            . DIRECTORY_SEPARATOR
            . '.yassg.php';

        /** @var Configuration $configuration */
        $configuration = include $fixtureConfigurationFilePathname;

        $this->setTemporaryDirectoryPath(
            $configuration->getOutputDirectory(),
        );

        $command = (new Container($fixtureConfigurationFilePathname))
            ->get(BuildCommand::class);

        $commandTester = $this->runCommand($command, $fixtureDirectory);

        $this->assertEquals(
            Command::SUCCESS,
            $commandTester->getStatusCode(),
        );
        $this->assertDirectoryEquals(
            $fixtureDirectory . DIRECTORY_SEPARATOR . 'expected',
            $configuration->getOutputDirectory(),
        );
        $this->assertSummaryMatches(
            $fixtureDirectory . DIRECTORY_SEPARATOR . 'expected',
            $commandTester,
        );
    }

    public function testRunningTheBuildCommandWithADirectoryOfTwigFiles(): void
    {
        $fixtureDirectory = self::$fixturesDirectory
            . DIRECTORY_SEPARATOR
            . 'just-twig-files';
        $fixtureConfigurationFilePathname = $fixtureDirectory
            . DIRECTORY_SEPARATOR
            . '.yassg.php';

        /** @var Configuration $configuration */
        $configuration = include $fixtureConfigurationFilePathname;

        $this->setTemporaryDirectoryPath(
            $configuration->getOutputDirectory(),
        );

        $command = (new Container($fixtureConfigurationFilePathname))
            ->get(BuildCommand::class);

        $commandTester = $this->runCommand($command, $fixtureDirectory);

        $this->assertEquals(
            Command::SUCCESS,
            $commandTester->getStatusCode(),
        );
        $this->assertDirectoryEquals(
            $fixtureDirectory . DIRECTORY_SEPARATOR . 'expected',
            $configuration->getOutputDirectory(),
        );
        $this->assertSummaryMatches(
            $fixtureDirectory . DIRECTORY_SEPARATOR . 'expected',
            $commandTester,
        );
    }

    public function testRunningTheBuildCommandWithAProjectThatUsesMetadata(): void
    {
        $fixtureDirectory = self::$fixturesDirectory
            . DIRECTORY_SEPARATOR
            . 'metadata';
        $fixtureConfigurationFilePathname = $fixtureDirectory
            . DIRECTORY_SEPARATOR
            . '.yassg.php';

        /** @var Configuration $configuration */
        $configuration = include $fixtureConfigurationFilePathname;

        $this->setTemporaryDirectoryPath(
            $configuration->getOutputDirectory(),
        );

        $command = (new Container($fixtureConfigurationFilePathname))
            ->get(BuildCommand::class);

        $commandTester = $this->runCommand($command, $fixtureDirectory);

        $this->assertEquals(
            Command::SUCCESS,
            $commandTester->getStatusCode(),
        );
        $this->assertDirectoryEquals(
            $fixtureDirectory . DIRECTORY_SEPARATOR . 'expected',
            $configuration->getOutputDirectory(),
        );
        $this->assertSummaryMatches(
            $fixtureDirectory . DIRECTORY_SEPARATOR . 'expected',
            $commandTester,
        );
    }

    public function testRunningTheBuildCommandWithAProjectThatUsesAPlugin(): void
    {
        $fixtureDirectory = self::$fixturesDirectory
            . DIRECTORY_SEPARATOR
            . 'plugins';
        $fixtureConfigurationFilePathname = $fixtureDirectory
            . DIRECTORY_SEPARATOR
            . '.yassg.php';

        /** @var Configuration $configuration */
        $configuration = include $fixtureConfigurationFilePathname;

        $this->setTemporaryDirectoryPath(
            $configuration->getOutputDirectory(),
        );

        $command = (new Container($fixtureConfigurationFilePathname))
            ->get(BuildCommand::class);

        $commandTester = $this->runCommand($command, $fixtureDirectory);

        $this->assertEquals(
            Command::SUCCESS,
            $commandTester->getStatusCode(),
        );
        $this->assertDirectoryEquals(
            $fixtureDirectory . DIRECTORY_SEPARATOR . 'expected',
            $configuration->getOutputDirectory(),
        );
        $this->assertSummaryMatches(
            $fixtureDirectory . DIRECTORY_SEPARATOR . 'expected',
            $commandTester,
        );
    }

    public function testRunningTheBuildCommandWithoutAConfigurationFile(): void
    {
        $fixtureDirectory = self::$fixturesDirectory
            . DIRECTORY_SEPARATOR
            . 'no-configuration';

        $this->setTemporaryDirectoryPath(
            $fixtureDirectory . DIRECTORY_SEPARATOR . 'public',
        );

        chdir($fixtureDirectory);

        /** @var BuildCommand $command */
        $command = (new Container(null))->get(BuildCommand::class);

        $commandTester = $this->runCommand($command, $fixtureDirectory);

        $this->assertEquals(
            Command::SUCCESS,
            $commandTester->getStatusCode(),
        );
        $this->assertDirectoryEquals(
            $fixtureDirectory . DIRECTORY_SEPARATOR . 'src',
            $fixtureDirectory . DIRECTORY_SEPARATOR . 'public',
        );
        $this->assertSummaryMatches(
            $fixtureDirectory . DIRECTORY_SEPARATOR . 'src',
            $commandTester,
        );
    }

    public function testRunningTheBuildCommandWithAProjectThatUsesTheSlugPlugin(): void
    {
        $fixtureDirectory = self::$fixturesDirectory
            . DIRECTORY_SEPARATOR
            . 'plugin-slug-basic';
        $fixtureConfigurationFilePathname = $fixtureDirectory
            . DIRECTORY_SEPARATOR
            . '.yassg.php';

        /** @var Configuration $configuration */
        $configuration = include $fixtureConfigurationFilePathname;

        $this->setTemporaryDirectoryPath(
            $configuration->getOutputDirectory(),
        );

        $command = (new Container($fixtureConfigurationFilePathname))
            ->get(BuildCommand::class);

        $commandTester = $this->runCommand($command, $fixtureDirectory);

        $this->assertEquals(
            Command::SUCCESS,
            $commandTester->getStatusCode(),
        );
        $this->assertDirectoryEquals(
            $fixtureDirectory . DIRECTORY_SEPARATOR . 'expected',
            $configuration->getOutputDirectory(),
        );
        $this->assertSummaryMatches(
            $fixtureDirectory . DIRECTORY_SEPARATOR . 'expected',
            $commandTester,
        );
    }
}
