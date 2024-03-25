<?php

/*
 * Copyright (c) 2022 Damien Dart, <damiendart@pobox.com>.
 * This file is distributed under the MIT licence. For more information,
 * please refer to the accompanying "LICENCE" file.
 */

declare(strict_types=1);

namespace Yassg\Plugins\Slug;

/**
 * @psalm-api
 */
class BasicSlugStrategy implements SlugStrategyInterface
{
    public function slugify(string $input): string
    {
        error_clear_last();

        $input = preg_replace('/^\/|(index)?.(html?|md|php)|(.twig)?$/', '', $input);

        if (PREG_NO_ERROR !== preg_last_error() || null === $input) {
            throw new \RuntimeException(preg_last_error_msg());
        }

        return ltrim($input, '/');
    }
}
