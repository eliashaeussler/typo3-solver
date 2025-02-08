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

namespace EliasHaeussler\Typo3Solver\Tests\Functional\Middleware;

use EliasHaeussler\Typo3Solver as Src;
use EliasHaeussler\Typo3Solver\Tests;
use PHPUnit\Framework;
use TYPO3\TestingFramework;

/**
 * PingMiddlewareTest
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
#[Framework\Attributes\CoversClass(Src\Middleware\PingMiddleware::class)]
final class PingMiddlewareTest extends TestingFramework\Core\Functional\FunctionalTestCase
{
    use Tests\InternalRequestTrait;

    protected array $testExtensionsToLoad = [
        'typo3conf/ext/solver/Tests/Functional/Fixtures/Extensions/middleware_bridge',
    ];

    protected bool $initializeDatabase = false;

    #[Framework\Attributes\Test]
    public function middlewareIsSkippedOnUnsupportedRequest(): void
    {
        $request = self::createRequest('/tx_solver/ping')->withoutHeader('Accept');
        $response = $this->executeFrontendSubRequest($request);

        self::assertNotSame(200, $response->getStatusCode());
    }

    #[Framework\Attributes\Test]
    public function middlewareIsSkippedOnNonMatchingRoute(): void
    {
        $request = self::createRequest('/');
        $response = $this->executeFrontendSubRequest($request);

        self::assertNotSame(200, $response->getStatusCode());
    }
}
