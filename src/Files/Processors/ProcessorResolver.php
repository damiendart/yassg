<?php

/*
 * Copyright (c) 2022 Damien Dart, <damiendart@pobox.com>.
 * This file is distributed under the MIT licence. For more information,
 * please refer to the accompanying "LICENCE" file.
 */

declare(strict_types=1);

namespace Yassg\Files\Processors;

use Yassg\Files\InputFileInterface;

class ProcessorResolver
{
    /**
     * @var ProcessorInterface[]
     */
    private array $processors;

    private DefaultProcessor $fallbackProcessor;

    public function __construct(DefaultProcessor $defaultProcessor)
    {
        $this->fallbackProcessor = $defaultProcessor;
        $this->processors = [];
    }

    public function addProcessor(ProcessorInterface $processor): self
    {
        $this->processors[] = $processor;

        return $this;
    }

    public function getApplicableProcessor(
        InputFileInterface $inputFile,
    ): ProcessorInterface {
        foreach ($this->processors as $processor) {
            if ($processor->canProcess($inputFile)) {
                return $processor;
            }
        }

        return $this->fallbackProcessor;
    }
}
