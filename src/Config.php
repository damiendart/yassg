<?php

// Copyright (C) 2021 Damien Dart, <damiendart@pobox.com>.
// This file is distributed under the MIT licence. For more information,
// please refer to the accompanying "LICENCE" file.

declare(strict_types=1);

namespace Yassg;

use Yassg\Processors\DefaultProcessor;

class Config
{
    public function getProcessors(): array
    {
        return [
            new DefaultProcessor(),
        ];
    }
}
