<?php

// Copyright (c) 2022 Damien Dart, <damiendart@pobox.com>.
// This file is distributed under the MIT licence. For more information,
// please refer to the accompanying "LICENCE" file.

declare(strict_types=1);

use League\CommonMark\ConverterInterface;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\MarkdownConverter;
use Psr\Container\ContainerInterface;

return [
    ConverterInterface::class => function (ContainerInterface $c): ConverterInterface {
        /** @var Environment $environment */
        $environment = $c->get(Environment::class);

        return new MarkdownConverter($environment);
    },
    Environment::class => function (ContainerInterface $c): Environment {
        /** @var CommonMarkCoreExtension $commonMarkCoreExtension */
        $commonMarkCoreExtension = $c->get(
            CommonMarkCoreExtension::class,
        );

        $environment = new Environment();

        /** @var GithubFlavoredMarkdownExtension $githubFlavouredMarkdownExtension */
        $githubFlavouredMarkdownExtension = $c->get(
            GithubFlavoredMarkdownExtension::class,
        );

        $environment
            ->addExtension($commonMarkCoreExtension)
            ->addExtension($githubFlavouredMarkdownExtension);

        return $environment;
    },
];
