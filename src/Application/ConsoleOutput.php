<?php

/*
 * Copyright (C) Damien Dart, <damiendart@pobox.com>.
 * This file is distributed under the MIT licence. For more information,
 * please refer to the accompanying "LICENCE" file.
 */

declare(strict_types=1);

namespace Yassg\Application;

class ConsoleOutput implements OutputInterface
{
    /** @var resource */
    private $standardErrorStream;

    /** @var resource */
    private $standardOutputStream;

    private int $verbosity = OutputInterface::VERBOSITY_NORMAL;

    /**
     * @param resource $standardErrorStream
     * @param resource $standardOutputStream
     */
    public function __construct(
        $standardErrorStream,
        $standardOutputStream,
    ) {
        $this->standardErrorStream = $standardErrorStream;
        $this->standardOutputStream = $standardOutputStream;
    }

    public function isVerbose(): bool
    {
        return OutputInterface::VERBOSITY_VERBOSE === $this->verbosity;
    }

    // This method is final to appease Psalm; for more information,
    // please see <https://psalm.dev/074>.
    final public function setVerbosity(int $level): void
    {
        $this->verbosity = $level;
    }

    public function write(string $output): self
    {
        fwrite($this->standardOutputStream, $output);

        return $this;
    }

    public function writeError(string $output): self
    {
        fwrite($this->standardErrorStream, $output);

        return $this;
    }
}
