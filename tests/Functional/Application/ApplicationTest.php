<?php

// Copyright (c) 2022 Damien Dart, <damiendart@pobox.com>.
// This file is distributed under the MIT licence. For more information,
// please refer to the accompanying "LICENCE" file.

declare(strict_types=1);

namespace Yassg\Tests\Functional\Application;

use Yassg\Application\Application;
use Yassg\Configuration\Configuration;

/**
 * @internal
 * @coversNothing
 */
class ApplicationTest extends ApplicationTestBase
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

        $application = new Application($this->consoleOutput);

        $this->assertEquals(
            Application::RETURN_FAILURE,
            $application->run(
                ['yassg', '--config', $fixtureConfigurationFilePathname],
            ),
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

        $application = new Application($this->consoleOutput);

        $this->assertEquals(
            Application::RETURN_SUCCESS,
            $application->run(
                ['yassg', '--config', $fixtureConfigurationFilePathname],
            ),
        );
        $this->assertDirectoryEquals(
            $configuration->getInputDirectory(),
            $configuration->getOutputDirectory(),
        );
        $this->assertSummaryMatches($configuration->getInputDirectory());
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

        $application = new Application($this->consoleOutput);

        $this->assertEquals(
            Application::RETURN_SUCCESS,
            $application->run(
                ['yassg', '--config', $fixtureConfigurationFilePathname],
            ),
        );
        $this->assertDirectoryEquals(
            $fixtureDirectory . DIRECTORY_SEPARATOR . 'expected',
            $configuration->getOutputDirectory(),
        );
        $this->assertSummaryMatches(
            $fixtureDirectory . DIRECTORY_SEPARATOR . 'expected',
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

        $application = new Application($this->consoleOutput);

        $this->assertEquals(
            Application::RETURN_SUCCESS,
            $application->run(
                ['yassg', '--config', $fixtureConfigurationFilePathname],
            ),
        );
        $this->assertDirectoryEquals(
            $fixtureDirectory . DIRECTORY_SEPARATOR . 'expected',
            $configuration->getOutputDirectory(),
        );
        $this->assertSummaryMatches(
            $fixtureDirectory . DIRECTORY_SEPARATOR . 'expected',
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

        $application = new Application($this->consoleOutput);

        $this->assertEquals(
            Application::RETURN_SUCCESS,
            $application->run(
                ['yassg', '--config', $fixtureConfigurationFilePathname],
            ),
        );
        $this->assertDirectoryEquals(
            $fixtureDirectory . DIRECTORY_SEPARATOR . 'expected',
            $configuration->getOutputDirectory(),
        );
        $this->assertSummaryMatches(
            $fixtureDirectory . DIRECTORY_SEPARATOR . 'expected',
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

        $application = new Application($this->consoleOutput);

        $this->assertEquals(
            Application::RETURN_SUCCESS,
            $application->run(
                ['yassg', '--config', $fixtureConfigurationFilePathname],
            ),
        );
        $this->assertDirectoryEquals(
            $fixtureDirectory . DIRECTORY_SEPARATOR . 'expected',
            $configuration->getOutputDirectory(),
        );
        $this->assertSummaryMatches(
            $fixtureDirectory . DIRECTORY_SEPARATOR . 'expected',
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

        $application = new Application($this->consoleOutput);

        $this->assertEquals(
            Application::RETURN_SUCCESS,
            $application->run(['yassg']),
        );
        $this->assertDirectoryEquals(
            $fixtureDirectory . DIRECTORY_SEPARATOR . 'src',
            $fixtureDirectory . DIRECTORY_SEPARATOR . 'public',
        );
        $this->assertSummaryMatches(
            $fixtureDirectory . DIRECTORY_SEPARATOR . 'src',
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

        $application = new Application($this->consoleOutput);

        $this->assertEquals(
            Application::RETURN_SUCCESS,
            $application->run(
                ['yassg', '--config', $fixtureConfigurationFilePathname],
            ),
        );
        $this->assertDirectoryEquals(
            $fixtureDirectory . DIRECTORY_SEPARATOR . 'expected',
            $configuration->getOutputDirectory(),
        );
        $this->assertSummaryMatches(
            $fixtureDirectory . DIRECTORY_SEPARATOR . 'expected',
        );
    }
}
