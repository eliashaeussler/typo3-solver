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
use PHPUnit\Framework;
use TYPO3\TestingFramework;

use function json_encode;

/**
 * JsonFormatterTest
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
final class JsonFormatterTest extends TestingFramework\Core\Unit\UnitTestCase
{
    private Src\Formatter\JsonFormatter $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new Src\Formatter\JsonFormatter();
    }

    #[Framework\Attributes\Test]
    public function formatReturnsJsonSerializedSolution(): void
    {
        $problem = Src\Tests\Unit\DataProvider\ProblemDataProvider::get();
        $solution = Src\Tests\Unit\DataProvider\SolutionDataProvider::get();

        self::assertJsonStringEqualsJsonString(
            json_encode($solution, JSON_THROW_ON_ERROR),
            $this->subject->format($problem, $solution),
        );
    }
}
