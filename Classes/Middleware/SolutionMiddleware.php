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

namespace EliasHaeussler\Typo3Solver\Middleware;

use EliasHaeussler\SSE;
use EliasHaeussler\Typo3Solver\Authentication;
use EliasHaeussler\Typo3Solver\Cache;
use EliasHaeussler\Typo3Solver\Configuration;
use EliasHaeussler\Typo3Solver\Exception;
use EliasHaeussler\Typo3Solver\Formatter;
use EliasHaeussler\Typo3Solver\ProblemSolving;
use EliasHaeussler\Typo3Solver\Utility;
use Psr\Http\Message;
use Psr\Http\Server;
use TYPO3\CMS\Core;

/**
 * SolutionMiddleware
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
final readonly class SolutionMiddleware implements Server\MiddlewareInterface
{
    public const ROUTE_PATH = '/tx_solver/solution';

    public function __construct(
        private Configuration\Configuration $configuration,
        private Cache\ExceptionsCache $exceptionsCache,
        private Formatter\Message\ExceptionStreamFormatter $exceptionFormatter,
        private Formatter\WebStreamFormatter $webFormatter,
        private Authentication\StreamAuthentication $authentication,
    ) {}

    /**
     * @throws SSE\Exception\StreamIsActive
     * @throws SSE\Exception\StreamIsClosed
     * @throws SSE\Exception\StreamIsInactive
     * @throws \JsonException
     */
    public function process(
        Message\ServerRequestInterface $request,
        Server\RequestHandlerInterface $handler,
    ): Message\ResponseInterface {
        // Pass through request if it's not supported
        if (!$this->isRequestSupported($request)) {
            return $handler->handle($request);
        }

        // Create event stream
        $eventStream = SSE\Stream\SelfEmittingEventStream::create();
        $eventStream->open();

        try {
            // Get exception identifier and stream hash
            $hash = $request->getQueryParams()['hash'] ?? null;
            $id = $request->getQueryParams()['exception'] ?? null;

            // Throw exception if stream hash is invalid
            if (!\is_string($hash)) {
                throw Exception\AuthenticationFailureException::create();
            }

            // Authenticate with stream hash
            $this->authentication->authenticate($hash);

            // Throw exception if exception identifier is invalid
            if (!\is_string($id)) {
                throw Exception\UnrecoverableExceptionException::forMissingIdentifier();
            }

            // Restore exception
            $exception = $this->exceptionsCache->get($id);

            // Throw exception if original exception cannot be restored
            if ($exception === null) {
                throw Exception\UnrecoverableExceptionException::create($id);
            }

            // Create solver
            $solver = new ProblemSolving\Solver($this->configuration, $this->webFormatter);

            // Send solution stream
            foreach ($solver->solveStreamed($exception) as $solution) {
                $eventStream->sendMessage('solutionDelta', $solution);
            }
        } catch (\Throwable $exception) {
            $eventStream->sendMessage('solutionError', $this->exceptionFormatter->format($exception));
        } finally {
            $eventStream->close('solutionFinished');
        }

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
