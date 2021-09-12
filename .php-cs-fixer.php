<?php

// Copyright (C) 2021 Damien Dart, <damiendart@pobox.com>.
// This file is distributed under the MIT licence. For more information,
// please refer to the accompanying "LICENCE" file.

declare(strict_types=1);

use ToolboxSass\Helpers\PHPCSFixerHelper;

return (new PhpCsFixer\Config())
    ->setRules(PHPCSFixerHelper::getHouseRules())
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in(__DIR__ . DIRECTORY_SEPARATOR . 'bin')
            ->in(__DIR__ . DIRECTORY_SEPARATOR . 'src')
            ->in(__DIR__ . DIRECTORY_SEPARATOR . 'tests')
            ->name('*.php')
            ->name('yassg')
    );
