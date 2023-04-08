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

namespace EliasHaeussler\Typo3Solver\Tests\Unit\Utility;

use EliasHaeussler\Typo3Solver as Src;
use TYPO3\CMS\Core;
use TYPO3\TestingFramework;

/**
 * HttpUtilityTest
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
final class HttpUtilityTest extends TestingFramework\Core\Unit\UnitTestCase
{
    /**
     * @test
     */
    public function uriMatchesRequestReturnsTrueIfGivenRoutePathMatchesRequestUri(): void
    {
        $routePath = '/foo';
        $request = new Core\Http\ServerRequest('https://www.example.com/foo');

        self::assertTrue(Src\Utility\HttpUtility::uriMatchesRequest($routePath, $request));
    }

    /**
     * @test
     */
    public function uriMatchesRequestReturnsFalseIfGivenRoutePathDoesNotMatchRequestUri(): void
    {
        $routePath = '/foo';
        $request = new Core\Http\ServerRequest('https://www.example.com/baz');

        self::assertFalse(Src\Utility\HttpUtility::uriMatchesRequest($routePath, $request));
    }

    /**
     * @test
     */
    public function getServerRequestReturnsGlobalServerRequest(): void
    {
        $request = new Core\Http\ServerRequest();

        $GLOBALS['TYPO3_REQUEST'] = $request;

        self::assertSame($request, Src\Utility\HttpUtility::getServerRequest());

        unset($GLOBALS['TYPO3_REQUEST']);
    }

    /**
     * @test
     */
    public function getServerRequestCreatesServerRequestIfNoGlobalServerRequestIsAvailable(): void
    {
        $emptyStream = new Core\Http\Stream('php://temp');

        // Simulate TYPO3 request url
        Core\Utility\GeneralUtility::setIndpEnv('TYPO3_REQUEST_URL', 'https://www.example.com');

        $expected = Core\Http\ServerRequestFactory::fromGlobals()->withBody($emptyStream);
        $actual = Src\Utility\HttpUtility::getServerRequest()->withBody($emptyStream);

        self::assertEquals($expected, $actual);

        // Revert simulated TYPO3 request url
        Core\Utility\GeneralUtility::flushInternalRuntimeCaches();
    }
}
