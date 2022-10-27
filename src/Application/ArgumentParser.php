<?php

/*
 * Copyright (c) 2022 Damien Dart, <damiendart@pobox.com>.
 * This file is distributed under the MIT licence. For more information,
 * please refer to the accompanying "LICENCE" file.
 */

declare(strict_types=1);

namespace Yassg\Application;

class ArgumentParser
{
    private ?string $configurationFilePathname = null;
    private bool $helpFlag = false;
    private bool $verboseFlag = false;

    /** @param string[] $argv */
    public function __construct(array $argv)
    {
        $this->parseArguments($argv);
    }

    public function getConfigurationFilePathname(): ?string
    {
        return $this->configurationFilePathname;
    }

    public function isHelpFlagSet(): bool
    {
        return $this->helpFlag;
    }

    public function isVerboseFlagSet(): bool
    {
        return $this->verboseFlag;
    }

    /** @param string[] $arguments */
    private function parseArguments(array $arguments): void
    {
        /** @var ?string $currentOption */
        $currentOption = null;

        /** @var string[] $normalisedArguments */
        $normalisedArguments = [];

        foreach ($arguments as $token) {
            if (str_contains($token, '=')) {
                $normalisedArguments = array_merge(
                    $normalisedArguments,
                    explode('=', $token, 2),
                );
            } elseif (preg_match('/^-[a-zA-Z\d]{2,}/', $token)) {
                $normalisedArguments = array_merge(
                    $normalisedArguments,
                    array_map(
                        fn (string $item): string => "-{$item}",
                        str_split(ltrim($token, '-')),
                    ),
                );
            } else {
                $normalisedArguments[] = $token;
            }
        }

        for ($i = 1; $i < count($normalisedArguments); ++$i) {
            if ('--' === $normalisedArguments[$i]) {
                throw new InvalidArgumentException(
                    'Positional command-line arguments are not accepted.',
                );
            }

            if (in_array($normalisedArguments[$i], ['-h', '--help'])) {
                $this->helpFlag = true;
            } elseif (in_array($normalisedArguments[$i], ['-v', '--verbose'])) {
                $this->verboseFlag = true;
            } elseif (in_array($normalisedArguments[$i], ['-c', '--config'])) {
                $currentOption = $normalisedArguments[$i];
            } else {
                if (in_array($currentOption, ['-c', '--config'])) {
                    if (str_starts_with($normalisedArguments[$i], '-')) {
                        throw new InvalidArgumentException(
                            "Missing value for \"{$currentOption}\".",
                        );
                    }

                    if (!$this->isHelpFlagSet()) {
                        $this->configurationFilePathname = $normalisedArguments[$i];
                    }

                    $currentOption = null;
                } else {
                    throw new InvalidArgumentException(
                        "Invalid argument or option: \"{$normalisedArguments[$i]}\".",
                    );
                }
            }
        }

        if (null !== $currentOption) {
            throw new InvalidArgumentException(
                "Missing value for \"{$currentOption}\".",
            );
        }
    }
}
