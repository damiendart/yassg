<?php

// Copyright (c) 2022 Damien Dart, <damiendart@pobox.com>.
// This file is distributed under the MIT licence. For more information,
// please refer to the accompanying "LICENCE" file.

declare(strict_types=1);

namespace Yassg\Tests\Functional\Commands;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

abstract class CommandTestBase extends TestCase
{
    protected static Filesystem $filesystem;
    protected static string $fixturesDirectory;
    protected ?string $temporaryDirectoryPath = null;

    public static function setUpBeforeClass(): void
    {
        self::$filesystem = new Filesystem();
        self::$fixturesDirectory = dirname(__DIR__, 2)
            . DIRECTORY_SEPARATOR
            . 'Fixtures';
    }

    protected function setUp(): void
    {
        $this->temporaryDirectoryPath = null;
    }

    protected function tearDown(): void
    {
        if ($this->temporaryDirectoryPath) {
            self::$filesystem->remove($this->temporaryDirectoryPath);
        }
    }

    protected function runCommand(
        Command $command,
        ?string $cwd = null,
        array $input = [],
    ): CommandTester {
        if ($cwd) {
            chdir($cwd);
        }

        $command->setApplication(new Application());

        $commandTester = new CommandTester($command);

        $commandTester->execute($input);

        return $commandTester;
    }

    /**
     * Sets the directory that is deleted once a test is complete to
     * prevent any inaccurate results due to stale files.
     */
    protected function setTemporaryDirectoryPath(
        string $temporaryDirectoryPath,
    ): void {
        $this->temporaryDirectoryPath = $temporaryDirectoryPath;
    }

    protected function assertDirectoryEquals(
        string $expectedDirectoryPath,
        string $inputDirectoryPath,
    ): void {
        $expectedFiles = (new Finder())->files()->in($expectedDirectoryPath);
        $inputFiles = (new Finder())->files()->in($inputDirectoryPath);

        $this->assertSame($expectedFiles->count(), $inputFiles->count());

        foreach ($inputFiles as $file) {
            $this->assertFileEquals(
                $file->getRealPath(),
                $expectedDirectoryPath
                    . DIRECTORY_SEPARATOR
                    . $file->getRelativePathname(),
            );
        }
    }
}
