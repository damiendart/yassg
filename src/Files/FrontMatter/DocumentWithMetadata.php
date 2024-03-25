<?php

/*
 * Copyright (C) Damien Dart, <damiendart@pobox.com>.
 * This file is distributed under the MIT licence. For more information,
 * please refer to the accompanying "LICENCE" file.
 */

declare(strict_types=1);

namespace Yassg\Files\FrontMatter;

class DocumentWithMetadata
{
    /** @param mixed[] $metadata */
    public function __construct(
        private array $metadata = [],
        private string $content = '',
    ) {}

    public function getContent(): string
    {
        return $this->content;
    }

    /** @return mixed[] */
    public function getMetadata(): array
    {
        return $this->metadata;
    }
}
