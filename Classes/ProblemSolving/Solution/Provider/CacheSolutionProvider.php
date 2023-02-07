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

namespace EliasHaeussler\Typo3Solver\ProblemSolving\Solution\Provider;

use EliasHaeussler\Typo3Solver\Cache;
use EliasHaeussler\Typo3Solver\ProblemSolving;

/**
 * CacheSolutionProvider.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
final class CacheSolutionProvider implements SolutionProvider
{
    public function __construct(
        private readonly Cache\SolutionsCache $cache,
        private readonly SolutionProvider $provider,
    ) {
    }

    public function getSolution(ProblemSolving\Problem\Problem $problem): ProblemSolving\Solution\Solution
    {
        $solution = $this->cache->get($problem);

        if ($solution === null) {
            $solution = $this->provider->getSolution($problem);
            $this->cache->set($problem, $solution);
        }

        return $solution;
    }

    public function canBeUsed(ProblemSolving\Problem\Problem $problem): bool
    {
        return $this->provider->canBeUsed($problem);
    }

    public function getProvider(): SolutionProvider
    {
        return $this->provider;
    }
}
