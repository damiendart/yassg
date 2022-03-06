<?php

// Copyright (c) 2022 Damien Dart, <damiendart@pobox.com>.
// This file is distributed under the MIT licence. For more information,
// please refer to the accompanying "LICENCE" file.

declare(strict_types=1);

use Yassg\Configuration\Configuration;
use Yassg\Plugins\Collections\CollectionsPlugin;

return (new Configuration(
    __DIR__ . DIRECTORY_SEPARATOR . 'input',
    __DIR__ . DIRECTORY_SEPARATOR . 'output',
))
    ->addPlugin(new CollectionsPlugin());
