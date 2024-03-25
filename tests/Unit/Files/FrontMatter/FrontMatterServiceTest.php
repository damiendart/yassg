<?php

/*
 * Copyright (C) Damien Dart, <damiendart@pobox.com>.
 * This file is distributed under the MIT licence. For more information,
 * please refer to the accompanying "LICENCE" file.
 */

declare(strict_types=1);

namespace Yassg\Tests\Unit\Files\FrontMatter;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Parser;
use Yassg\Files\FrontMatter\FrontMatterService;

/**
 * @covers \Yassg\Files\FrontMatter\FrontMatterService
 *
 * @internal
 */
class FrontMatterServiceTest extends TestCase
{
    public function testParsingADocumentWithValidFrontMatter(): void
    {
        $frontMatterService = new FrontMatterService(new Parser());

        $document = $frontMatterService->parse(
            "---\nfoo: bar\n---\nLorem ipsum dolor sit amet.",
        );

        $this->assertEquals(['foo' => 'bar'], $document->getMetadata());
        $this->assertEquals(
            'Lorem ipsum dolor sit amet.',
            $document->getContent(),
        );
    }

    public function testParsingDocumentsWithCustomFrontMatterDelimeters(): void
    {
        $frontMatterService = new FrontMatterService(new Parser());
        $testStrings = [
            "<!---\nfoo: bar\n--->\nLorem ipsum dolor sit amet.",
            "{#---\nfoo: bar\n---#}\nLorem ipsum dolor sit amet.",
        ];

        foreach ($testStrings as $testString) {
            $document = $frontMatterService->parse(
                $testString,
            );

            $this->assertEquals(['foo' => 'bar'], $document->getMetadata());
            $this->assertEquals(
                'Lorem ipsum dolor sit amet.',
                $document->getContent(),
            );
        }
    }

    public function testParsingADocumentWithInvalidFrontMatter(): void
    {
        $frontMatterService = new FrontMatterService(new Parser());
        $this->expectException(ParseException::class);

        $frontMatterService->parse(
            "---\n{foo: bar\n---\nLorem ipsum dolor sit amet.",
        );
    }

    public function testParsingADocumentWithJustFrontMatter(): void
    {
        $frontMatterService = new FrontMatterService(new Parser());

        $document = $frontMatterService->parse(
            "---\nfoo: bar\n---\n",
        );

        $this->assertEquals(['foo' => 'bar'], $document->getMetadata());
        $this->assertEquals('', $document->getContent());
    }

    public function testParsingADocumentWithFrontMatterContainingADelimeter(): void
    {
        $frontMatterService = new FrontMatterService(new Parser());

        $document = $frontMatterService->parse(
            "---\n---foo: bar\n---\n",
        );

        $this->assertEquals(['---foo' => 'bar'], $document->getMetadata());
        $this->assertEquals('', $document->getContent());
    }

    public function testParsingADocumentWithIndentedFrontMatter(): void
    {
        $frontMatterService = new FrontMatterService(new Parser());

        $document = $frontMatterService->parse(
            "---\n    foo: bar\n    baz: qux\n---\n",
        );

        $this->assertEquals(
            ['foo' => 'bar', 'baz' => 'qux'],
            $document->getMetadata(),
        );
        $this->assertEquals('', $document->getContent());
    }

    public function testParsingDocumentsWithJustComments(): void
    {
        $frontMatterService = new FrontMatterService(new Parser());
        $testStrings = [
            "{# This shouldn't be parsed as front matter #}\n",
            "<!-- This shouldn't be parsed as front matter -->\n",
        ];

        foreach ($testStrings as $testString) {
            $document = $frontMatterService->parse($testString);

            $this->assertEmpty($document->getMetadata());
            $this->assertEquals($testString, $document->getContent());
        }
    }
}
