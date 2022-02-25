<?php

// Copyright (c) 2022 Damien Dart, <damiendart@pobox.com>.
// This file is distributed under the MIT licence. For more information,
// please refer to the accompanying "LICENCE" file.

declare(strict_types=1);

namespace Yassg\Container\Definitions;

use Symfony\Component\Finder\Finder;

return [
    Finder::class => function (): Finder {
        return (new Finder())
            ->ignoreDotFiles(true);
    },
];
