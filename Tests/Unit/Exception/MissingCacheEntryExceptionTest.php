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

namespace EliasHaeussler\Typo3Solver\Tests\Unit\Exception;

use EliasHaeussler\Typo3Solver as Src;
use TYPO3\TestingFramework;

/**
 * MissingCacheEntryExceptionTest
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
final class MissingCacheEntryExceptionTest extends TestingFramework\Core\Unit\UnitTestCase
{
    /**
     * @test
     */
    public function createReturnsExceptionForMissingCacheEntry(): void
    {
        $actual = Src\Exception\MissingCacheEntryException::create('foo');

        self::assertSame('Unable to find a cache entry for identifier "foo".', $actual->getMessage());
        self::assertSame(1680388703, $actual->getCode());
    }
}
