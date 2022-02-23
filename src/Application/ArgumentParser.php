<?php

// Copyright (c) 2022 Damien Dart, <damiendart@pobox.com>.
// This file is distributed under the MIT licence. For more information,
// please refer to the accompanying "LICENCE" file.

declare(strict_types=1);

namespace Yassg\Application;

use Yassg\Exceptions\InvalidArgumentException;

class ArgumentParser
{
    private ?string $configurationFilePathname = null;
    private bool $helpFlag = false;
    private bool $verboseFlag = false;

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

    private function parseArguments(array $arguments): void
    {
        /** @var ?string $currentArgument */
        $currentOption = null;

        /** @var string[] $normalisedArguments */
        $normalisedArguments = [];

        /** @var string $token */
        foreach ($arguments as $token) {
            if (str_contains($token, '=')) {
                $normalisedArguments = array_merge(
                    $normalisedArguments,
                    explode('=', $token, 2),
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

            if (in_array($normalisedArguments[$i], ['-h', '--help'], true)) {
                $this->helpFlag = true;
            } elseif ('--verbose' === $normalisedArguments[$i]) {
                $this->verboseFlag = true;
            } elseif (
                str_starts_with($normalisedArguments[$i], '-c')
                || str_starts_with($normalisedArguments[$i], '--config')
            ) {
                $currentOption = $normalisedArguments[$i];
            } else {
                if (in_array($currentOption, ['-c', '--config'])) {
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
