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

namespace EliasHaeussler\Typo3Solver\Tests;

use TYPO3\CMS\Core;
use TYPO3\TestingFramework;

/**
 * InternalRequestTrait
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
trait InternalRequestTrait
{
    private static function createRequest(string $path): TestingFramework\Core\Functional\Framework\Frontend\InternalRequest
    {
        $uri = new Core\Http\Uri('http://typo3-testing.local');
        $uri = $uri->withPath($path);
        $request = new TestingFramework\Core\Functional\Framework\Frontend\InternalRequest((string)$uri);

        return $request->withHeader('Accept', 'text/event-stream');
    }
}
