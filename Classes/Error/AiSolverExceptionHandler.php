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

namespace EliasHaeussler\Typo3Solver\Error;

use EliasHaeussler\Typo3Solver\Cache;
use EliasHaeussler\Typo3Solver\Configuration;
use EliasHaeussler\Typo3Solver\Exception;
use EliasHaeussler\Typo3Solver\Formatter;
use EliasHaeussler\Typo3Solver\Middleware;
use EliasHaeussler\Typo3Solver\ProblemSolving;
use EliasHaeussler\Typo3Solver\Utility;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\RequestOptions;
use Throwable;
use TYPO3\CMS\Core;

/**
 * AiSolverExceptionHandler.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
final class AiSolverExceptionHandler extends Core\Error\DebugExceptionHandler
{
    private readonly Client $client;
    private readonly Configuration\Configuration $configuration;
    private readonly Formatter\Message\ExceptionFormatter $exceptionFormatter;
    private readonly Cache\ExceptionsCache $exceptionsCache;
    private readonly Cache\SolutionsCache $solutionsCache;
    private readonly Formatter\WebFormatter $webFormatter;

    public function __construct()
    {
        parent::__construct();

        $this->client = $this->createClient();
        $this->configuration = new Configuration\Configuration();
        $this->exceptionFormatter = new Formatter\Message\ExceptionFormatter();
        $this->exceptionsCache = new Cache\ExceptionsCache();
        $this->solutionsCache = new Cache\SolutionsCache();
        $this->webFormatter = new Formatter\WebFormatter();
    }

    public function echoExceptionCLI(Throwable $exception): void
    {
        try {
            $solver = new ProblemSolving\Solver($this->configuration->getProvider(), new Formatter\CliFormatter());
            $solution = $solver->solve($exception);

            if ($solution !== null) {
                echo $solution;
            }
        } catch (Exception\UnableToSolveException) {
            // Intended fallthrough.
        }

        parent::echoExceptionCLI($exception);
    }

    protected function getContent(Throwable $throwable): string
    {
        $content = parent::getContent($throwable);
        $serverRequest = Utility\HttpUtility::getServerRequest();

        // Early return if solver is disabled
        if ($serverRequest->getQueryParams()['disableSolver'] ?? false) {
            return $content;
        }

        $solutionProvider = $this->configuration->getProvider();

        // Use solution stream if possible
        if ($solutionProvider->canBeUsed($throwable) && $this->isStreamedResponseSupported()) {
            $this->exceptionsCache->set($throwable);

            $solutionProvider = new ProblemSolving\Solution\Provider\DelegatingCacheSolutionProvider(
                $this->solutionsCache,
                $solutionProvider,
            );
        }

        try {
            $solver = new ProblemSolving\Solver($solutionProvider, $this->webFormatter);
            $solution = $solver->solve($throwable);

            if ($solution !== null) {
                $solution .= $this->webFormatter->getAdditionalScripts();
            }
        } catch (Throwable $exception) {
            $solution = $this->exceptionFormatter->format($exception);
        }

        if ($solution === null) {
            return $content;
        }

        return Utility\StringUtility::replaceFirstOccurrence(
            '<div class="trace">',
            $solution . '<div class="trace">',
            $content,
        );
    }

    protected function getStylesheet(): string
    {
        return parent::getStylesheet() . $this->webFormatter->getAdditionalStyles();
    }

    private function isStreamedResponseSupported(): bool
    {
        $serverRequest = Utility\HttpUtility::getServerRequest();
        $pingUri = $serverRequest->getUri()
            ->withPath(Middleware\PingMiddleware::ROUTE_PATH)
            ->withQuery('disableSolver=1')
        ;
        $pingResponse = $this->client->request('GET', $pingUri, [
            RequestOptions::HEADERS => [
                'Accept' => 'text/event-stream',
            ],
        ]);

        return $pingResponse->getStatusCode() === 200;
    }

    private function createClient(): Client
    {
        $handler = HandlerStack::create();
        $handler->remove('http_errors');

        return new Client(['handler' => $handler]);
    }
}
