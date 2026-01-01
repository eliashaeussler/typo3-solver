<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS extension "solver".
 *
 * Copyright (C) 2023-2026 Elias Häußler <elias@haeussler.dev>
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
 * AuthenticationFailureExceptionTest
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
#[Framework\Attributes\CoversClass(Src\Exception\AuthenticationFailureException::class)]
final class AuthenticationFailureExceptionTest extends TestingFramework\Core\Unit\UnitTestCase
{
    #[Framework\Attributes\Test]
    public function createReturnsExceptionForFailedAuthentication(): void
    {
        $actual = Src\Exception\AuthenticationFailureException::create();

        self::assertSame('Unable to authenticate for solution stream.', $actual->getMessage());
        self::assertSame(1681218115, $actual->getCode());
    }
}
