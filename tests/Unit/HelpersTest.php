<?php

// Copyright (c) 2022 Damien Dart, <damiendart@pobox.com>.
// This file is distributed under the MIT licence. For more information,
// please refer to the accompanying "LICENCE" file.

declare(strict_types=1);

namespace Yassg\Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class HelpersTest extends TestCase
{
    /** @dataProvider dedentTestStringProvider */
    public function testDedentingAString(
        string $input,
        string $expected,
    ): void {
        $this->assertEquals($expected, dedent($input));
    }

    public function dedentTestStringProvider(): array
    {
        return [
            [
                'Lorem ipsum.',
                'Lorem ipsum.',
            ],
            [
                '  Lorem ipsum.',
                'Lorem ipsum.',
            ],
            [
                "Lorem ipsum.\nLorem ipsum.\nLorem ipsum.",
                "Lorem ipsum.\nLorem ipsum.\nLorem ipsum.",
            ],
            [
                "  Lorem ipsum.\n  Lorem ipsum.\n  Lorem ipsum.",
                "Lorem ipsum.\nLorem ipsum.\nLorem ipsum.",
            ],
            [
                "    Lorem ipsum.\n  Lorem ipsum.\n  Lorem ipsum.",
                "  Lorem ipsum.\nLorem ipsum.\nLorem ipsum.",
            ],
            [
                "    Lorem ipsum.\n  Lorem ipsum.\n      Lorem ipsum.",
                "  Lorem ipsum.\nLorem ipsum.\n    Lorem ipsum.",
            ],
            [
                "\tLorem ipsum.\n\tLorem ipsum.\n\tLorem ipsum.",
                "Lorem ipsum.\nLorem ipsum.\nLorem ipsum.",
            ],
            [
                "\t\tLorem ipsum.\n\tLorem ipsum.\n\t\t\tLorem ipsum.",
                "\tLorem ipsum.\nLorem ipsum.\n\t\tLorem ipsum.",
            ],
        ];
    }
}
