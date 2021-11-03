<?php

// Copyright (C) 2021 Damien Dart, <damiendart@pobox.com>.
// This file is distributed under the MIT licence. For more information,
// please refer to the accompanying "LICENCE" file.

declare(strict_types=1);

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\MarkdownConverter;
use League\CommonMark\MarkdownConverterInterface;
use Psr\Container\ContainerInterface;

return [
    MarkdownConverterInterface::class => function (ContainerInterface $c): MarkdownConverter {
        return new MarkdownConverter($c->get(Environment::class));
    },
    Environment::class => function (ContainerInterface $c): Environment {
        $environment = new Environment();

        $environment
            ->addExtension($c->get(CommonMarkCoreExtension::class))
            ->addExtension(
                $c->get(GithubFlavoredMarkdownExtension::class),
            );

        return $environment;
    },
];
