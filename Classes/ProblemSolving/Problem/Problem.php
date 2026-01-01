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

namespace EliasHaeussler\Typo3Solver\ProblemSolving\Problem;

use EliasHaeussler\Typo3Solver\ProblemSolving;

/**
 * Problem
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
final readonly class Problem
{
    public function __construct(
        private \Throwable $exception,
        private ProblemSolving\Solution\Provider\SolutionProvider $solutionProvider,
        private string $prompt,
    ) {}

    public function getException(): \Throwable
    {
        return $this->exception;
    }

    public function getSolutionProvider(): ProblemSolving\Solution\Provider\SolutionProvider
    {
        return $this->solutionProvider;
    }

    public function getPrompt(): string
    {
        return $this->prompt;
    }
}
