<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS extension "solver".
 *
 * Copyright (C) 2023 Elias Häußler <elias@haeussler.dev>
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

namespace EliasHaeussler\Typo3Solver\Tests\Unit\Formatter\Message;

use EliasHaeussler\Typo3Solver as Src;
use EliasHaeussler\Typo3Solver\Tests;
use Exception;
use TYPO3\TestingFramework;

/**
 * ExceptionFormatterTest
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
final class ExceptionFormatterTest extends TestingFramework\Core\Unit\UnitTestCase
{
    use Tests\DOMDocumentTrait;

    private Src\Formatter\Message\ExceptionFormatter $subject;

    protected function setUp(): void
    {
        $this->subject = new Src\Formatter\Message\ExceptionFormatter();
    }

    /**
     * @test
     */
    public function formatReturnsFormattedException(): void
    {
        $exception = new Exception('Something went wrong.', 123);

        $actual = $this->subject->format($exception);
        $xpath = self::createDOMXPath($actual);

        self::assertNodeContentEqualsString('#123', '//div/ul[1]/li[1]/div[1]/span[1]/text()', $xpath);
        self::assertNodeContentEqualsString('Something went wrong.', '//div/ul[1]/li[1]/div[1]/span[1]/following-sibling::text()[1]', $xpath);
    }
}
