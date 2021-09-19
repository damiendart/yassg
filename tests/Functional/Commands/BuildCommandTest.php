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

/**
 * @internal
 * @coversNothing
 */
class BuildCommandTest extends TestCase
{
    private static Filesystem $filesystem;
    private static string $fixturesDirectoryPath;
    private static string $temporaryDirectoryPath;

    public static function setUpBeforeClass(): void
    {
        self::$filesystem = new Filesystem();
        self::$fixturesDirectoryPath = dirname(__DIR__, 2)
            . DIRECTORY_SEPARATOR
            . 'Fixtures';
        self::$temporaryDirectoryPath = dirname(__DIR__, 2)
            . DIRECTORY_SEPARATOR
            . 'tmp';
    }

    protected function tearDown(): void
    {
        self::$filesystem->remove(self::$temporaryDirectoryPath);
    }

    public function testRunningTheBuildCommandWithAnInvalidInputDirectory(): void
    {
        $command = (new ContainerBuilder())
            ->build()
            ->get(BuildCommand::class);

        $command->setApplication(new Application());

        $commandTester = new CommandTester($command);

        $commandTester->execute(
            [
                'inputDirectory' => 'non-existent-directory',
                'outputDirectory' => self::$temporaryDirectoryPath,
            ],
        );

        $this->assertEquals(
            Command::FAILURE,
            $commandTester->getStatusCode(),
        );
        $this->assertDirectoryDoesNotExist(self::$temporaryDirectoryPath);
    }

    public function testRunningTheBuildCommandWithADirectoryOfTextFiles(): void
    {
        $command = (new ContainerBuilder())
            ->build()
            ->get(BuildCommand::class);

        $command->setApplication(new Application());

        $commandTester = new CommandTester($command);

        $commandTester->execute(
            [
                'inputDirectory' => self::$fixturesDirectoryPath
                    . DIRECTORY_SEPARATOR
                    . 'just-text-files',
                'outputDirectory' => self::$temporaryDirectoryPath,
            ],
        );

        $this->assertEquals(Command::SUCCESS, $commandTester->getStatusCode());
        $this->assertFileEquals(
            self::$fixturesDirectoryPath
                . DIRECTORY_SEPARATOR
                . 'just-text-files'
                . DIRECTORY_SEPARATOR
                . 'test-1.txt',
            self::$temporaryDirectoryPath
                . DIRECTORY_SEPARATOR
                . 'test-1.txt',
        );
        $this->assertFileEquals(
            self::$fixturesDirectoryPath
                . DIRECTORY_SEPARATOR
                . 'just-text-files'
                . DIRECTORY_SEPARATOR
                . 'test-2.txt',
            self::$temporaryDirectoryPath
                . DIRECTORY_SEPARATOR
                . 'test-2.txt',
        );
    }
}
