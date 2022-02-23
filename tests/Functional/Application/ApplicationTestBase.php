<?php

// Copyright (c) 2022 Damien Dart, <damiendart@pobox.com>.
// This file is distributed under the MIT licence. For more information,
// please refer to the accompanying "LICENCE" file.

declare(strict_types=1);

namespace Yassg\Tests\Functional\Application;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Yassg\Application\ConsoleOutput;

abstract class ApplicationTestBase extends TestCase
{
    /** @var resource */
    protected $errorStream;

    protected static Filesystem $filesystem;
    protected static string $fixturesDirectory;
    protected ConsoleOutput $consoleOutput;

    /** @var resource */
    protected $standardStream;

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
        $this->errorStream = fopen('php://memory', 'a');
        $this->standardStream = fopen('php://memory', 'a');
        $this->temporaryDirectoryPath = null;

        $this->consoleOutput = new ConsoleOutput(
            $this->errorStream,
            $this->standardStream,
        );
    }

    protected function tearDown(): void
    {
        if ($this->temporaryDirectoryPath) {
            self::$filesystem->remove($this->temporaryDirectoryPath);
        }
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
                $expectedDirectoryPath
                    . DIRECTORY_SEPARATOR
                    . $file->getRelativePathname(),
                $file->getRealPath(),
            );
        }
    }

    protected function assertSummaryMatches(
        string $expectedDirectoryPath,
    ): void {
        $fileCount = (new Finder())
            ->files()
            ->in($expectedDirectoryPath)
            ->count();

        rewind($this->standardStream);

        $this->assertStringContainsString(
            sprintf(
                '%d file%s created',
                $fileCount,
                1 === $fileCount ? '' : 's',
            ),
            stream_get_contents($this->standardStream),
        );
    }
}
