<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS extension "solver".
 *
 * Copyright (C) 2024 Elias Häußler <elias@haeussler.dev>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

namespace EliasHaeussler\Typo3Solver\Tests\Unit\Formatter;

use EliasHaeussler\Typo3Solver as Src;
use EliasHaeussler\Typo3Solver\Tests;
use PHPUnit\Framework;
use TYPO3\TestingFramework;

use function json_decode;

/**
 * WebStreamFormatterTest
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
final class WebStreamFormatterTest extends TestingFramework\Core\Unit\UnitTestCase
{
    use Tests\DOMDocumentTrait;

    private Src\Formatter\WebStreamFormatter $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new Src\Formatter\WebStreamFormatter(new Src\View\TemplateRenderer());
    }

    #[Framework\Attributes\Test]
    public function formatReturnsJsonEncodedSolutionStream(): void
    {
        $problem = Src\Tests\Unit\DataProvider\ProblemDataProvider::get();
        $solution = Src\Tests\Unit\DataProvider\SolutionDataProvider::get(3);

        $actual = $this->subject->format($problem, $solution);

        self::assertJson($actual);

        $json = json_decode($actual, true, flags: JSON_THROW_ON_ERROR);

        self::assertIsArray($json);

        $data = $json['data'] ?? null;
        $content = $json['content'] ?? null;

        self::assertIsArray($data);
        self::assertIsString($content);

        $xpath = self::createDOMXPath($content);

        self::assertSame('model', $json['data']['model'] ?? null);
        self::assertSame(3, $json['data']['numberOfChoices'] ?? null);
        self::assertSame(3, $json['data']['numberOfPendingChoices'] ?? null);
        self::assertSame('prompt', $json['data']['prompt'] ?? null);

        // First choice
        self::assertNodeContentEqualsString('0', '//ul/li[1]/@data-solution-choice-index', $xpath);
        self::assertNodeContentEqualsString('solution-0', '//ul/li[1]/input[1]/@id', $xpath);
        self::assertNodeContentEqualsString('solution-2', '//ul/li[1]/label[1]/@for', $xpath);
        self::assertNodeContentEqualsString('solution-1', '//ul/li[1]/label[2]/@for', $xpath);
        self::assertNodeContentEqualsString('message 1', '//ul/li[1]/div[1]/p[1]/text()', $xpath);

        // Second choice
        self::assertNodeContentEqualsString('1', '//ul/li[2]/@data-solution-choice-index', $xpath);
        self::assertNodeContentEqualsString('solution-1', '//ul/li[2]/input[1]/@id', $xpath);
        self::assertNodeContentEqualsString('solution-0', '//ul/li[2]/label[1]/@for', $xpath);
        self::assertNodeContentEqualsString('solution-2', '//ul/li[2]/label[2]/@for', $xpath);
        self::assertNodeContentEqualsString('message 2', '//ul/li[2]/div[1]/p[1]/text()', $xpath);

        // Third choice
        self::assertNodeContentEqualsString('2', '//ul/li[3]/@data-solution-choice-index', $xpath);
        self::assertNodeContentEqualsString('solution-2', '//ul/li[3]/input[1]/@id', $xpath);
        self::assertNodeContentEqualsString('solution-1', '//ul/li[3]/label[1]/@for', $xpath);
        self::assertNodeContentEqualsString('solution-0', '//ul/li[3]/label[2]/@for', $xpath);
        self::assertNodeContentEqualsString('message 3', '//ul/li[3]/div[1]/p[1]/text()', $xpath);

        // Fourth choice (should not exist)
        self::assertNodeListIsEmpty('//ul/li[4]', $xpath);
    }
}
