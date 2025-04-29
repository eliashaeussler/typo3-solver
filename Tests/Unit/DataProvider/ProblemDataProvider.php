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

namespace EliasHaeussler\Typo3Solver\Tests\Unit\DataProvider;

use EliasHaeussler\Typo3Solver\ProblemSolving;
use EliasHaeussler\Typo3Solver\Tests;

/**
 * ProblemDataProvider
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 * @internal
 */
final class ProblemDataProvider
{
    public static function get(
        string $message = 'Something went wrong.',
        ?ProblemSolving\Solution\Provider\SolutionProvider $solutionProvider = null,
        string $prompt = 'prompt',
    ): ProblemSolving\Problem\Problem {
        $exception = new \Exception($message, 123);
        $solutionProvider ??= new Tests\Unit\Fixtures\DummySolutionProvider();

        return new ProblemSolving\Problem\Problem($exception, $solutionProvider, $prompt);
    }
}
