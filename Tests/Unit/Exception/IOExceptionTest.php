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

namespace EliasHaeussler\Typo3Solver\Tests\Unit\Exception;

use EliasHaeussler\Typo3Solver as Src;
use PHPUnit\Framework;
use TYPO3\TestingFramework;

/**
 * IOExceptionTest
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
final class IOExceptionTest extends TestingFramework\Core\Unit\UnitTestCase
{
    #[Framework\Attributes\Test]
    public function forConflictingParametersReturnsExceptionForConflictingParameters(): void
    {
        $actual = Src\Exception\IOException::forConflictingParameters('foo', 'baz');

        self::assertSame('The parameters "foo", "baz" cannot be used together.', $actual->getMessage());
        self::assertSame(1680388489, $actual->getCode());
    }

    #[Framework\Attributes\Test]
    public function forMissingRequiredParameterReturnsExceptionForMissingRequiredParameter(): void
    {
        $actual = Src\Exception\IOException::forMissingRequiredParameter('foo');

        self::assertSame('The parameter "foo" is required.', $actual->getMessage());
        self::assertSame(1680388939, $actual->getCode());
    }
}
