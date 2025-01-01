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

namespace EliasHaeussler\Typo3Solver\Tests\Unit\Formatter\Message;

use EliasHaeussler\Typo3Solver as Src;
use EliasHaeussler\Typo3Solver\Tests;
use PHPUnit\Framework;
use TYPO3\TestingFramework;

/**
 * ExceptionStreamFormatterTest
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
final class ExceptionStreamFormatterTest extends TestingFramework\Core\Unit\UnitTestCase
{
    use Tests\DOMDocumentTrait;

    private Src\Formatter\Message\ExceptionStreamFormatter $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new Src\Formatter\Message\ExceptionStreamFormatter(new Src\View\TemplateRenderer());
    }

    #[Framework\Attributes\Test]
    public function formatReturnsJsonEncodedExceptionStream(): void
    {
        $exception = new \Exception('Something went wrong.', 123);

        $actual = $this->subject->format($exception);

        self::assertJson($actual);

        $json = json_decode($actual, true, flags: JSON_THROW_ON_ERROR);

        self::assertIsArray($json);

        $data = $json['data'] ?? null;
        $content = $json['content'] ?? null;

        self::assertIsArray($data);
        self::assertIsString($content);

        $xpath = self::createDOMXPath($content);

        self::assertSame('Something went wrong.', $json['data']['message'] ?? null);
        self::assertSame(123, $json['data']['code'] ?? null);

        self::assertNodeContentEqualsString('#123', '//div/ul[1]/li[1]/div[1]/span[1]/text()', $xpath);
        self::assertNodeContentEqualsString('Something went wrong.', '//div/ul[1]/li[1]/div[1]/span[1]/following-sibling::text()[1]', $xpath);
    }
}
