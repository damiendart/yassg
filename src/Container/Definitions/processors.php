<?php

// Copyright (C) 2021 Damien Dart, <damiendart@pobox.com>.
// This file is distributed under the MIT licence. For more information,
// please refer to the accompanying "LICENCE" file.

declare(strict_types=1);

namespace Yassg\Container\Definitions;

use Psr\Container\ContainerInterface;
use Yassg\Processors\DefaultProcessor;
use Yassg\Processors\MarkdownProcessor;
use Yassg\Processors\ProcessorResolver;
use Yassg\Processors\TwigProcessor;

return [
    ProcessorResolver::class => function (ContainerInterface $c): ProcessorResolver {
        /** @var DefaultProcessor $defaultProcessor */
        $defaultProcessor = $c->get(DefaultProcessor::class);

        /** @var MarkdownProcessor $markdownProcessor */
        $markdownProcessor = $c->get(MarkdownProcessor::class);

        /** @var TwigProcessor $twigProcessor */
        $twigProcessor = $c->get(TwigProcessor::class);

        $processorResolver = new ProcessorResolver($defaultProcessor);

        $processorResolver
            ->addProcessor($markdownProcessor)
            ->addProcessor($twigProcessor);

        return $processorResolver;
    },
];
