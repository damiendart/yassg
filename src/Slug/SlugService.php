<?php

/*
 * Copyright (c) 2022 Damien Dart, <damiendart@pobox.com>.
 * This file is distributed under the MIT licence. For more information,
 * please refer to the accompanying "LICENCE" file.
 */

declare(strict_types=1);

namespace Yassg\Slug;

class SlugService
{
    private SlugStrategyInterface $slugStrategy;

    public function __construct(SlugStrategyInterface $slugStrategy)
    {
        $this->slugStrategy = $slugStrategy;
    }

    public function slugify(string $input): string
    {
        return $this->slugStrategy->slugify($input);
    }
}
