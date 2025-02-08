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

namespace EliasHaeussler\Typo3Solver\Middleware;

use EliasHaeussler\SSE;
use EliasHaeussler\Typo3Solver\Utility;
use Psr\Http\Message;
use Psr\Http\Server;
use TYPO3\CMS\Core;

/**
 * PingMiddleware
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
final class PingMiddleware implements Server\MiddlewareInterface
{
    public const ROUTE_PATH = '/tx_solver/ping';

    /**
     * @throws SSE\Exception\StreamIsActive
     * @throws SSE\Exception\StreamIsClosed
     * @throws SSE\Exception\StreamIsInactive
     */
    public function process(
        Message\ServerRequestInterface $request,
        Server\RequestHandlerInterface $handler,
    ): Message\ResponseInterface {
        // Pass through request if it's not supported
        if (!$this->isRequestSupported($request)) {
            return $handler->handle($request);
        }

        $eventStream = SSE\Stream\SelfEmittingEventStream::create();
        $eventStream->open();
        $eventStream->close();

        return new Core\Http\Response();
    }

    private function isRequestSupported(Message\ServerRequestInterface $request): bool
    {
        if (!SSE\Stream\SelfEmittingEventStream::canHandle($request)) {
            return false;
        }

        return Utility\HttpUtility::uriMatchesRequest(self::ROUTE_PATH, $request);
    }
}
