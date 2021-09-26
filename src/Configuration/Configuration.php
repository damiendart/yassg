<?php

// Copyright (C) 2021 Damien Dart, <damiendart@pobox.com>.
// This file is distributed under the MIT licence. For more information,
// please refer to the accompanying "LICENCE" file.

declare(strict_types=1);

namespace Yassg\Configuration;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Yassg\Processors\DefaultProcessor;
use Yassg\Processors\ProcessorInterface;

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

    public function getProcessors(): array
    {
        return $this->options['processors'];
    }

    private function createResolver(): OptionsResolver
    {
        $optionsResolver = new OptionsResolver();

        $optionsResolver
            ->setRequired(
                ['inputDirectory', 'outputDirectory'],
            )
            ->setDefaults(
                [
                    'processors' => [
                        new DefaultProcessor(),
                    ],
                ],
            )
            ->setAllowedTypes('inputDirectory', 'string')
            ->setAllowedTypes('outputDirectory', 'string')
            ->setAllowedTypes('processors', ProcessorInterface::class . '[]');

        return $optionsResolver;
    }
}
