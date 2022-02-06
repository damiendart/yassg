<?php

// Copyright (c) 2022 Damien Dart, <damiendart@pobox.com>.
// This file is distributed under the MIT licence. For more information,
// please refer to the accompanying "LICENCE" file.

declare(strict_types=1);

namespace Yassg\Plugins\Slug;

use function DI\decorate;
use Yassg\Files\Metadata\MetadataExtractorInterface;
use Yassg\Plugins\PluginInterface;
use Yassg\Services\Slug\SlugStrategyInterface;

class SlugPlugin implements PluginInterface
{
    private SlugStrategyInterface $slugStrategy;

    public function __construct(SlugStrategyInterface $slugStrategy)
    {
        $this->slugStrategy = $slugStrategy;
    }

    public function getContainerDefinitions(): array
    {
        return [
            MetadataExtractorInterface::class => decorate(
                function (MetadataExtractorInterface $previous) {
                    return new SlugMetadataExtractor(
                        $this->slugStrategy,
                        $previous,
                    );
                },
            ),
        ];
    }
}
