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

namespace EliasHaeussler\Typo3Solver\Middleware;

use EliasHaeussler\Typo3Solver\Cache;
use EliasHaeussler\Typo3Solver\Configuration;
use EliasHaeussler\Typo3Solver\Exception;
use EliasHaeussler\Typo3Solver\Formatter;
use EliasHaeussler\Typo3Solver\Http;
use EliasHaeussler\Typo3Solver\ProblemSolving;
use EliasHaeussler\Typo3Solver\Utility;
use Psr\Http\Message;
use Psr\Http\Server;
use Throwable;
use TYPO3\CMS\Core;

/**
 * SolutionMiddleware
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
final class SolutionMiddleware implements Server\MiddlewareInterface
{
    public const ROUTE_PATH = '/tx_solver/solution';

    public function __construct(
        private readonly Configuration\Configuration $configuration,
        private readonly Cache\ExceptionsCache $exceptionsCache,
        private readonly Formatter\Message\ExceptionStreamFormatter $exceptionFormatter,
    ) {
    }

    /**
     * @throws Exception\EventStreamException
     */
    public function process(
        Message\ServerRequestInterface $request,
        Server\RequestHandlerInterface $handler,
    ): Message\ResponseInterface {
        // Pass through request if it's not supported
        if (!$this->isRequestSupported($request)) {
            return $handler->handle($request);
        }

        // Get exception identifier
        $id = $request->getQueryParams()['exception'] ?? null;

        // Early return if exception identifier is invalid
        if (!is_string($id) || ($exception = $this->exceptionsCache->get($id)) === null) {
            return new Core\Http\Response();
        }

        // Create solver
        $solver = new ProblemSolving\Solver($this->configuration->getProvider(), new Formatter\WebStreamFormatter());

        // Create event stream
        $eventStream = Http\EventStream::create($id);

        // Send solution stream
        try {
            foreach ($solver->solveStreamed($exception) as $solution) {
                $eventStream->sendMessage('solutionDelta', $solution);
            }
        } catch (Throwable $exception) {
            $eventStream->sendMessage('solutionError', $this->exceptionFormatter->format($exception));
        }

        // Finish event stream
        $eventStream->close('solutionFinished');

        return new Core\Http\Response();
    }

    private function isRequestSupported(Message\ServerRequestInterface $request): bool
    {
        if ($request->getHeader('Accept') !== ['text/event-stream']) {
            return false;
        }

        return Utility\HttpUtility::uriMatchesRequest(self::ROUTE_PATH, $request);
    }
}
