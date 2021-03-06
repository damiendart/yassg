<?php

/*
 * Copyright (c) 2022 Damien Dart, <damiendart@pobox.com>.
 * This file is distributed under the MIT licence. For more information,
 * please refer to the accompanying "LICENCE" file.
 */

declare(strict_types=1);

namespace Yassg\Services\FrontMatter;

use Symfony\Component\Yaml\Parser;

class FrontMatterService
{
    private const FRONT_MATTER_REGEXES = [
        '/^---\n(.*?)\n---\n(.*)/s',
        '/^{#---\n(.*?)\n---#}\n(.*)/s',
        '/^<!---\n(.*?)\n--->\n(.*)/s',
    ];

    private Parser $yamlParser;

    public function __construct(Parser $yamlParser)
    {
        $this->yamlParser = $yamlParser;
    }

    public function parse(string $input): DocumentWithMetadata
    {
        foreach (self::FRONT_MATTER_REGEXES as $regex) {
            if (
                1 === preg_match($regex, $input, $matches)
                && '' !== trim($matches[1])
            ) {
                /** @var array $frontMatter */
                $frontMatter = $this->yamlParser->parse(dedent($matches[1]));

                return new DocumentWithMetadata(
                    $frontMatter,
                    $matches[2],
                );
            }
        }

        return new DocumentWithMetadata([], $input);
    }
}
