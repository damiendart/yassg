<?php

/*
 * Copyright (c) 2022 Damien Dart, <damiendart@pobox.com>.
 * This file is distributed under the MIT licence. For more information,
 * please refer to the accompanying "LICENCE" file.
 */

declare(strict_types=1);

namespace Yassg\Files\Metadata;

use Yassg\Files\InputFile;
use Yassg\Services\FrontMatter\FrontMatterService;

class FrontMatterExtractor implements MetadataExtractorInterface
{
    private MetadataExtractorInterface $innerMetadataExtractor;
    private FrontMatterService $frontMatterService;

    public function __construct(
        MetadataExtractorInterface $metadataExtractor,
        FrontMatterService $frontMatterService,
    ) {
        $this->innerMetadataExtractor = $metadataExtractor;
        $this->frontMatterService = $frontMatterService;
    }

    public function addMetadata(InputFile $inputFile): void
    {
        $this->innerMetadataExtractor->addMetadata($inputFile);

        $parsedDocument = $this->frontMatterService->parse(
            $inputFile->getContent(),
        );

        $inputFile->setMetadata($parsedDocument->getMetadata());
        $inputFile->setContent($parsedDocument->getContent());
    }
}
