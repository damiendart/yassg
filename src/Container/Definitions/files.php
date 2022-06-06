<?php

/*
 * Copyright (c) 2022 Damien Dart, <damiendart@pobox.com>.
 * This file is distributed under the MIT licence. For more information,
 * please refer to the accompanying "LICENCE" file.
 */

declare(strict_types=1);

namespace Yassg\Container\Definitions;

use Psr\Container\ContainerInterface;
use Yassg\Files\Metadata\FrontMatterExtractor;
use Yassg\Files\Metadata\MetadataExtractor;
use Yassg\Files\Metadata\MetadataExtractorInterface;
use Yassg\Services\FrontMatter\FrontMatterService;

return [
    MetadataExtractorInterface::class => function (ContainerInterface $c): FrontMatterExtractor {
        /** @var FrontMatterService $frontMatterParser */
        $frontMatterParser = $c->get(FrontMatterService::class);

        return new FrontMatterExtractor(new MetadataExtractor(), $frontMatterParser);
    },
];
