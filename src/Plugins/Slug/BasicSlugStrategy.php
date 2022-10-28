<?php

/*
 * Copyright (c) 2022 Damien Dart, <damiendart@pobox.com>.
 * This file is distributed under the MIT licence. For more information,
 * please refer to the accompanying "LICENCE" file.
 */

declare(strict_types=1);

namespace Yassg\Plugins\Slug;

use function Yassg\preg_replace_safe;

class BasicSlugStrategy implements SlugStrategyInterface
{
    public function slugify(string $input): string
    {
        $input = preg_replace_safe('/.twig$/', '', $input);

        if (1 !== preg_match('/(html?|md|php)$/', $input)) {
            return ltrim($input, '/');
        }

        return ltrim(
            preg_replace_safe('/^\/|(index)?.(html?|md|php)$/', '', $input),
            '/',
        );
    }
}
