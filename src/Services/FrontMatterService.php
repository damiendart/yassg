<?php

// Copyright (C) 2021 Damien Dart, <damiendart@pobox.com>.
// This file is distributed under the MIT licence. For more information,
// please refer to the accompanying "LICENCE" file.

declare(strict_types=1);

namespace Yassg\Services;

use Symfony\Component\Yaml\Parser;

class FrontMatterService
{
    private const FRONT_MATTER_REGEXES = [
        '/^---\n(.*?)\n---\n(.*)/s',
        '/^{#---\n(.*?)\n---#}\n(.*)/s',
    ];

    private Parser $yamlParser;

    public function __construct(Parser $yamlParser)
    {
        $this->yamlParser = $yamlParser;
    }

    public function parseFrontMatter(string $input): array
    {
        $frontMatter = [];

        foreach (self::FRONT_MATTER_REGEXES as $regex) {
            if (
                1 === preg_match($regex, $input, $matches)
                && '' !== trim($matches[1])
            ) {
                /** @var array $frontMatter */
                $frontMatter = $this->yamlParser->parse($matches[1]);

                break;
            }
        }

        return $frontMatter;
    }

    public function stripFrontMatter(string $input): string
    {
        foreach (self::FRONT_MATTER_REGEXES as $regex) {
            if (1 === preg_match($regex, $input, $matches)) {
                return $matches[2];
            }
        }

        return $input;
    }
}