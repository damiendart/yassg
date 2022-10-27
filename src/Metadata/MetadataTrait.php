<?php

/*
 * Copyright (c) 2022 Damien Dart, <damiendart@pobox.com>.
 * This file is distributed under the MIT licence. For more information,
 * please refer to the accompanying "LICENCE" file.
 */

declare(strict_types=1);

namespace Yassg\Metadata;

trait MetadataTrait
{
    /** @var array<array-key, mixed> */
    private array $metadata = [];

    /** @return array<array-key, mixed> */
    public function getMetadata(): array
    {
        return $this->metadata;
    }

    /** @param array<array-key, mixed> $metadata */
    public function mergeMetadata(array $metadata): self
    {
        $this->metadata = array_merge($this->metadata, $metadata);

        return $this;
    }

    /** @param array<array-key, mixed> $metadata */
    public function setMetadata(array $metadata): self
    {
        $this->metadata = $metadata;

        return $this;
    }
}
