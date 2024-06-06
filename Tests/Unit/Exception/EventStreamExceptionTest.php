<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS extension "solver".
 *
 * Copyright (C) 2023-2024 Elias Häußler <elias@haeussler.dev>
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

namespace EliasHaeussler\Typo3Solver\Tests\Unit\Exception;

use EliasHaeussler\Typo3Solver as Src;
use PHPUnit\Framework;
use TYPO3\TestingFramework;

/**
 * EventStreamExceptionTest
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
final class EventStreamExceptionTest extends TestingFramework\Core\Unit\UnitTestCase
{
    #[Framework\Attributes\Test]
    public function forActiveResponseReturnsExceptionForActiveResponse(): void
    {
        $actual = Src\Exception\EventStreamException::forActiveResponse();

        self::assertSame('An active response stream was detected.', $actual->getMessage());
        self::assertSame(1680364482, $actual->getCode());
    }

    #[Framework\Attributes\Test]
    public function forClosedStreamReturnsExceptionForClosedStream(): void
    {
        $actual = Src\Exception\EventStreamException::forClosedStream();

        self::assertSame('Stream is closed and cannot be reused. Crete a new event stream instead.', $actual->getMessage());
        self::assertSame(1680365007, $actual->getCode());
    }
}
