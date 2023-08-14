<?php

/*
 * Copyright (c) 2022 Damien Dart, <damiendart@pobox.com>.
 * This file is distributed under the MIT licence. For more information,
 * please refer to the accompanying "LICENCE" file.
 */

declare(strict_types=1);

namespace Yassg\Events;

use Twig\Environment;

class TwigEnvironmentCreatedEvent extends Event
{
    private Environment $environment;

    public function __construct(Environment $environment)
    {
        $this->environment = $environment;
    }

    /**
     * @psalm-api
     */
    public function getEnvironment(): Environment
    {
        return $this->environment;
    }
}
