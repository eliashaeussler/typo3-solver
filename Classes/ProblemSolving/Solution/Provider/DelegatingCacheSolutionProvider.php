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

namespace EliasHaeussler\Typo3Solver\ProblemSolving\Solution\Provider;

use EliasHaeussler\Typo3Solver\Cache;
use EliasHaeussler\Typo3Solver\Exception;
use EliasHaeussler\Typo3Solver\ProblemSolving;
use OpenAI\Responses;

/**
 * DelegatingCacheSolutionProvider
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 *
 * @internal
 */
final class DelegatingCacheSolutionProvider implements SolutionProvider
{
    public function __construct(
        private readonly Cache\SolutionsCache $cache,
        private readonly SolutionProvider $delegate,
    ) {}

    /**
     * @throws Exception\MissingSolutionProviderException
     */
    public static function create(SolutionProvider $delegate = null): static
    {
        if ($delegate === null) {
            throw Exception\MissingSolutionProviderException::forDelegate();
        }

        return new self(new Cache\SolutionsCache(), $delegate);
    }

    public function getSolution(ProblemSolving\Problem\Problem $problem): ProblemSolving\Solution\Solution
    {
        $cachedProblem = new ProblemSolving\Problem\Problem(
            $problem->getException(),
            $this->delegate,
            $problem->getPrompt(),
        );
        $cachedSolution = $this->cache->get($cachedProblem);

        if ($cachedSolution !== null) {
            return $cachedSolution;
        }

        $dummyChoice = Responses\Chat\CreateResponseChoice::from([
            'index' => 0,
            'message' => [
                'role' => '',
                'content' => 'Please wait…',
                'function_call' => null,
                'tool_calls' => null,
            ],
            'finish_reason' => null,
        ]);

        return new ProblemSolving\Solution\Solution([$dummyChoice], 'Please wait…', 'Please wait…');
    }

    public function canBeUsed(\Throwable $exception): bool
    {
        return true;
    }

    public function isCacheable(): bool
    {
        return false;
    }
}
