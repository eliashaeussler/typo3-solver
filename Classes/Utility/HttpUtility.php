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

namespace EliasHaeussler\Typo3Solver\Utility;

use Exception;
use Psr\Http\Message;
use Symfony\Component\Routing;
use TYPO3\CMS\Core;

/**
 * HttpUtility
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
final class HttpUtility
{
    public static function uriMatchesRequest(string $routePath, Message\RequestInterface $request): bool
    {
        $collection = new Core\Routing\RouteCollection();
        $collection->add('_default', new Core\Routing\Route($routePath));

        $urlMatcher = new Routing\Matcher\UrlMatcher(
            $collection,
            Routing\RequestContext::fromUri((string)$request->getUri()),
        );

        try {
            return (bool)$urlMatcher->match($request->getUri()->getPath());
        } catch (Exception) {
            return false;
        }
    }

    public static function getServerRequest(): Message\ServerRequestInterface
    {
        $serverRequest = $GLOBALS['TYPO3_REQUEST'] ?? null;

        if ($serverRequest instanceof Message\ServerRequestInterface) {
            return $serverRequest;
        }

        return Core\Http\ServerRequestFactory::fromGlobals();
    }
}
