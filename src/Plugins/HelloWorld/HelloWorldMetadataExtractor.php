<?php

/*
 * Copyright (c) 2022 Damien Dart, <damiendart@pobox.com>.
 * This file is distributed under the MIT licence. For more information,
 * please refer to the accompanying "LICENCE" file.
 */

declare(strict_types=1);

namespace Yassg\Plugins\HelloWorld;

use Yassg\Files\InputFile;
use Yassg\Files\Metadata\MetadataExtractorInterface;

class HelloWorldMetadataExtractor implements MetadataExtractorInterface
{
    private MetadataExtractorInterface $innerMetadataExtractor;

    public function __construct(
        MetadataExtractorInterface $metadataExtractor,
    ) {
        $this->innerMetadataExtractor = $metadataExtractor;
    }

    public function addMetadata(InputFile $inputFile): void
    {
        $this->innerMetadataExtractor->addMetadata($inputFile);

        $inputFile->mergeMetadata(
            [
                'hello' => 'Hello, World!',
            ],
        );
    }
}
