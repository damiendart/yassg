<?php

// Copyright (C) 2021 Damien Dart, <damiendart@pobox.com>.
// This file is distributed under the MIT licence. For more information,
// please refer to the accompanying "LICENCE" file.

declare(strict_types=1);

namespace Yassg\Tests\Functional\Commands;

use DI\ContainerBuilder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;
use Yassg\Commands\BuildCommand;
use Yassg\Configuration\Configuration;

/**
 * @internal
 * @coversNothing
 */
class BuildCommandTest extends TestCase
{
    private static Filesystem $filesystem;
    private static string $fixturesDirectoryPath;

    private string $outputDirectoryPath;

    public static function setUpBeforeClass(): void
    {
        self::$filesystem = new Filesystem();
        self::$fixturesDirectoryPath = dirname(__DIR__, 2)
            . DIRECTORY_SEPARATOR
            . 'Fixtures';
    }

    protected function tearDown(): void
    {
        self::$filesystem->remove($this->outputDirectoryPath);
    }

    public function testRunningTheBuildCommandWithAnInvalidInputDirectory(): void
    {
        $command = (new ContainerBuilder())
            ->build()
            ->get(BuildCommand::class);
        $fixtureDirectoryPath = self::$fixturesDirectoryPath
            . DIRECTORY_SEPARATOR
            . 'invalid-input-directory';

        /** @var Configuration $configuration */
        $configuration = include $fixtureDirectoryPath
            . DIRECTORY_SEPARATOR
            . '.yassg.php';

        $this->outputDirectoryPath = $configuration->getOutputDirectory();

        $command->setApplication(new Application());

        $commandTester = new CommandTester($command);

        chdir($fixtureDirectoryPath);
        $commandTester->execute([]);

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
        $command = (new ContainerBuilder())
            ->build()
            ->get(BuildCommand::class);
        $fixtureDirectoryPath = self::$fixturesDirectoryPath
            . DIRECTORY_SEPARATOR
            . 'just-text-files';

        /** @var Configuration $configuration */
        $configuration = include $fixtureDirectoryPath
            . DIRECTORY_SEPARATOR
            . '.yassg.php';

        $this->outputDirectoryPath = $configuration->getOutputDirectory();

        $command->setApplication(new Application());

        $commandTester = new CommandTester($command);

        chdir($fixtureDirectoryPath);
        $commandTester->execute([]);

        $this->assertEquals(Command::SUCCESS, $commandTester->getStatusCode());
        $this->assertFileEquals(
            $fixtureDirectoryPath
                . DIRECTORY_SEPARATOR
                . 'input'
                . DIRECTORY_SEPARATOR
                . 'test-1.txt',
            $fixtureDirectoryPath
                . DIRECTORY_SEPARATOR
                . 'output'
                . DIRECTORY_SEPARATOR
                . 'test-1.txt',
        );
        $this->assertFileEquals(
            $fixtureDirectoryPath
                . DIRECTORY_SEPARATOR
                . 'input'
                . DIRECTORY_SEPARATOR
                . 'test-2.txt',
            $fixtureDirectoryPath
                . DIRECTORY_SEPARATOR
                . 'output'
                . DIRECTORY_SEPARATOR
                . 'test-2.txt',
        );
    }
}
