<?php

/*
 * Copyright (c) 2022 Damien Dart, <damiendart@pobox.com>.
 * This file is distributed under the MIT licence. For more information,
 * please refer to the accompanying "LICENCE" file.
 */

declare(strict_types=1);

namespace Yassg\Plugins\Collections;

use function DI\decorate;

use Psr\Container\ContainerInterface;
use Yassg\Configuration\Configuration;
use Yassg\Events\EventDispatcher;
use Yassg\Events\PreSiteBuildEvent;
use Yassg\Files\InputFile;
use Yassg\Plugins\PluginInterface;

class CollectionsPlugin implements PluginInterface
{
    /** @codeCoverageIgnore  */
    public function getContainerDefinitions(): array
    {
        return [
            EventDispatcher::class => decorate(
                function (EventDispatcher $previous, ContainerInterface $c) {
                    /** @var Configuration $configuration */
                    $configuration = $c->get(Configuration::class);

                    $previous->addEventListener(
                        PreSiteBuildEvent::class,
                        function (PreSiteBuildEvent $event) use ($configuration): void {
                            /** @var array<string, InputFile[]> $collections */
                            $collections = [];

                            foreach ($event->getInputFiles() as $inputFile) {
                                if (
                                    \array_key_exists(
                                        'collections',
                                        $inputFile->getMetadata(),
                                    )
                                ) {
                                    /** @var iterable<string>|string $keys */
                                    $keys = $inputFile->getMetadata()['collections'];

                                    if (!is_iterable($keys)) {
                                        $keys = [$keys];
                                    }

                                    foreach ($keys as $key) {
                                        $collections[$key][] = $inputFile;
                                    }
                                }
                            }

                            if (!empty($collections)) {
                                $configuration->mergeMetadata(
                                    ['collections' => $collections],
                                );
                            }
                        },
                    );

                    return $previous;
                },
            ),
        ];
    }
}
