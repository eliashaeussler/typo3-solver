<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS extension "solver".
 *
 * Copyright (C) 2023-2025 Elias Häußler <elias@haeussler.dev>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
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

/**
 * WebFormatterTest
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
#[Framework\Attributes\CoversClass(Src\Formatter\WebFormatter::class)]
final class WebFormatterTest extends TestingFramework\Core\Unit\UnitTestCase
{
    use Tests\DOMDocumentTrait;

    private Src\Cache\ExceptionsCache $exceptionsCache;
    private Src\Formatter\WebFormatter $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->exceptionsCache = new Src\Cache\ExceptionsCache();
        $this->subject = new Src\Formatter\WebFormatter(
            $this->exceptionsCache,
            new Src\View\TemplateRenderer(),
            new Src\Authentication\StreamAuthentication(),
        );
    }

    #[Framework\Attributes\Test]
    public function formatReturnsFormattedSolution(): void
    {
        $problem = Src\Tests\Unit\DataProvider\ProblemDataProvider::get();
        $solution = Src\Tests\Unit\DataProvider\SolutionDataProvider::get(3);
        $exceptionIdentifier = $this->exceptionsCache->getIdentifier($problem->getException());

        $actual = $this->subject->format($problem, $solution);
        $xpath = self::createDOMXPath($actual);

        // Get last stream hash
        $streamHash = $this->getLastGeneratedStreamHash();

        // Exception identifier
        self::assertNodeContentEqualsString($exceptionIdentifier, '//form/@data-exception-id', $xpath);

        // Stream hash
        self::assertNodeContentEqualsString($streamHash, '//form/@data-stream-hash', $xpath);

        // Number of responses
        self::assertNodeContentEqualsString('3', '//form/div[1]/div[1]/div[1]/h3[1]/span[1]/span[2]/text()', $xpath);

        // Model
        self::assertNodeContentEqualsString('model', '//form/div[1]/div[1]/div[1]/ul[1]/li[1]/span[2]/text()', $xpath);

        // First response
        self::assertNodeContentEqualsString('0', '//form/div[2]/ul[1]/li[1]/@data-solution-response-index', $xpath);
        self::assertNodeContentEqualsString('solution-0', '//form/div[2]/ul[1]/li[1]/input[1]/@id', $xpath);
        self::assertNodeContentEqualsString('solution-2', '//form/div[2]/ul[1]/li[1]/label[1]/@for', $xpath);
        self::assertNodeContentEqualsString('solution-1', '//form/div[2]/ul[1]/li[1]/label[2]/@for', $xpath);
        self::assertNodeContentEqualsString('message 1', '//form/div[2]/ul[1]/li[1]/div[1]/p[1]/text()', $xpath);

        // Second response
        self::assertNodeContentEqualsString('1', '//form/div[2]/ul[1]/li[2]/@data-solution-response-index', $xpath);
        self::assertNodeContentEqualsString('solution-1', '//form/div[2]/ul[1]/li[2]/input[1]/@id', $xpath);
        self::assertNodeContentEqualsString('solution-0', '//form/div[2]/ul[1]/li[2]/label[1]/@for', $xpath);
        self::assertNodeContentEqualsString('solution-2', '//form/div[2]/ul[1]/li[2]/label[2]/@for', $xpath);
        self::assertNodeContentEqualsString('message 2', '//form/div[2]/ul[1]/li[2]/div[1]/p[1]/text()', $xpath);

        // Third response
        self::assertNodeContentEqualsString('2', '//form/div[2]/ul[1]/li[3]/@data-solution-response-index', $xpath);
        self::assertNodeContentEqualsString('solution-2', '//form/div[2]/ul[1]/li[3]/input[1]/@id', $xpath);
        self::assertNodeContentEqualsString('solution-1', '//form/div[2]/ul[1]/li[3]/label[1]/@for', $xpath);
        self::assertNodeContentEqualsString('solution-0', '//form/div[2]/ul[1]/li[3]/label[2]/@for', $xpath);
        self::assertNodeContentEqualsString('message 3', '//form/div[2]/ul[1]/li[3]/div[1]/p[1]/text()', $xpath);

        // Fourth response (should not exist)
        self::assertNodeListIsEmpty('//form/div[2]/ul[1]/li[4]', $xpath);

        // Prompt
        self::assertNodeContentEqualsString('prompt', '//form/details[1]/pre[1]/text()', $xpath);
    }

    #[Framework\Attributes\Test]
    public function getAdditionalStylesReturnsAdditionalStylesheet(): void
    {
        self::assertStringEqualsFile(
            \dirname(__DIR__, 3) . '/Resources/Public/Css/main.css',
            $this->subject->getAdditionalStyles(),
        );
    }

    #[Framework\Attributes\Test]
    public function getAdditionalScriptsReturnsAdditionalJavaScript(): void
    {
        self::assertStringEqualsFile(
            \dirname(__DIR__, 3) . '/Resources/Public/JavaScript/main.js',
            \substr($this->subject->getAdditionalScripts(), \strlen('<script>'), -\strlen('</script>')),
        );
    }

    private function getLastGeneratedStreamHash(): string
    {
        $registeredHashes = \file(\dirname(__DIR__, 3) . '/var/transient/tx_solver/stream_auth.txt');

        self::assertIsArray($registeredHashes);

        $lastHash = \end($registeredHashes);

        self::assertIsString($lastHash);

        return $lastHash;
    }
}
