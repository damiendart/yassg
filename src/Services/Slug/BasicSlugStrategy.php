<?php

// Copyright (c) 2022 Damien Dart, <damiendart@pobox.com>.
// This file is distributed under the MIT licence. For more information,
// please refer to the accompanying "LICENCE" file.

declare(strict_types=1);

namespace Yassg\Services\Slug;

class BasicSlugStrategy implements SlugStrategyInterface
{
    public function slugify(string $input): string
    {
        $input = preg_replace('/.twig$/', '', $input);

        if (1 !== preg_match('/(html?|php)$/', $input)) {
            return str_starts_with($input, '/') ? $input : "/{$input}";
        }

        return '/'
            . preg_replace('/^\/|(index)?.(html?|php)$/', '', $input);
    }
}
