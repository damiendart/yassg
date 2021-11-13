<?php

// Copyright (C) 2021 Damien Dart, <damiendart@pobox.com>.
// This file is distributed under the MIT licence. For more information,
// please refer to the accompanying "LICENCE" file.

declare(strict_types=1);

namespace Yassg\Processors;

use Yassg\Files\InputFileInterface;

class ProcessorResolver
{
    /**
     * @var ProcessorInterface[]
     */
    private array $processors;

    private DefaultProcessor $fallbackProcessor;

    public function __construct(
        DefaultProcessor $defaultProcessor,
        MarkdownProcessor $markdownProcessor,
    ) {
        $this->fallbackProcessor = $defaultProcessor;
        $this->processors = [];

        $this->addProcessor($markdownProcessor);
    }

    public function addProcessor(ProcessorInterface $processor): void
    {
        $this->processors[] = $processor;
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
