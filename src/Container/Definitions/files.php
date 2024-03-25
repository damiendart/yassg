<?php

/*
 * Copyright (C) Damien Dart, <damiendart@pobox.com>.
 * This file is distributed under the MIT licence. For more information,
 * please refer to the accompanying "LICENCE" file.
 */

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use Yassg\Files\FrontMatter\FrontMatterService;
use Yassg\Files\Metadata\FrontMatterExtractor;
use Yassg\Files\Metadata\MetadataExtractor;
use Yassg\Files\Metadata\MetadataExtractorInterface;

return [
    MetadataExtractorInterface::class => function (ContainerInterface $c): FrontMatterExtractor {
        /** @var FrontMatterService $frontMatterParser */
        $frontMatterParser = $c->get(FrontMatterService::class);

        return new FrontMatterExtractor(new MetadataExtractor(), $frontMatterParser);
    },
];
