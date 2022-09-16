<?php

/*
 * Copyright (c) 2022 Damien Dart, <damiendart@pobox.com>.
 * This file is distributed under the MIT licence. For more information,
 * please refer to the accompanying "LICENCE" file.
 */

declare(strict_types=1);

namespace Yassg\Plugins\Slug;

use Yassg\Files\InputFile;
use Yassg\Files\Metadata\MetadataExtractorInterface;
use Yassg\Slug\SlugStrategyInterface;

class SlugMetadataExtractor implements MetadataExtractorInterface
{
    private SlugStrategyInterface $slugStrategy;
    private MetadataExtractorInterface $innerMetadataExtractor;

    public function __construct(
        SlugStrategyInterface $slugStrategy,
        MetadataExtractorInterface $metadataExtractor,
    ) {
        $this->innerMetadataExtractor = $metadataExtractor;
        $this->slugStrategy = $slugStrategy;
    }

    public function addMetadata(InputFile $inputFile): void
    {
        $this->innerMetadataExtractor->addMetadata($inputFile);

        if (false === array_key_exists('slug', $inputFile->getMetadata())) {
            $inputFile->mergeMetadata(
                [
                    'slug' => $this->slugStrategy->slugify(
                        $inputFile->getRelativePathname(),
                    ),
                ],
            );
        }
    }
}
