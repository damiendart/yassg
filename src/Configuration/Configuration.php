<?php

// Copyright (C) 2021 Damien Dart, <damiendart@pobox.com>.
// This file is distributed under the MIT licence. For more information,
// please refer to the accompanying "LICENCE" file.

declare(strict_types=1);

namespace Yassg\Configuration;

use Symfony\Component\OptionsResolver\OptionsResolver;

class Configuration
{
    private array $options;

    public function __construct(array $options = [])
    {
        $this->options = $this->createResolver()->resolve($options);
    }

    public function getInputDirectory(): string
    {
        return $this->options['inputDirectory'];
    }

    public function getOutputDirectory(): string
    {
        return $this->options['outputDirectory'];
    }

    private function createResolver(): OptionsResolver
    {
        $optionsResolver = new OptionsResolver();

        $optionsResolver
            ->setRequired(
                ['inputDirectory', 'outputDirectory'],
            )
            ->setAllowedTypes('inputDirectory', 'string')
            ->setAllowedTypes('outputDirectory', 'string');

        return $optionsResolver;
    }
}
