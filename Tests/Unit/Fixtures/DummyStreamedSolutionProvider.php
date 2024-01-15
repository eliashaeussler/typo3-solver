<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS extension "solver".
 *
 * Copyright (C) 2024 Elias Häußler <elias@haeussler.dev>
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

namespace EliasHaeussler\Typo3Solver\Tests\Unit\Fixtures;

use EliasHaeussler\Typo3Solver\ProblemSolving;
use Throwable;
use Traversable;

/**
 * DummyStreamedSolutionProvider
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 * @internal
 */
final class DummyStreamedSolutionProvider implements ProblemSolving\Solution\Provider\StreamedSolutionProvider
{
    public ?ProblemSolving\Solution\Solution $solution = null;
    public bool $shouldBeUsed = true;

    /**
     * @var array<ProblemSolving\Solution\Solution>
     */
    public array $solutionStream = [];

    public static function create(): static
    {
        return new self();
    }

    public function getSolution(ProblemSolving\Problem\Problem $problem): ProblemSolving\Solution\Solution
    {
        return $this->solution ?? new ProblemSolving\Solution\Solution([], 'foo', 'baz');
    }

    public function getStreamedSolution(ProblemSolving\Problem\Problem $problem): Traversable
    {
        yield from $this->solutionStream;
    }

    public function canBeUsed(Throwable $exception): bool
    {
        return $this->shouldBeUsed;
    }

    public function isCacheable(): bool
    {
        return true;
    }
}
