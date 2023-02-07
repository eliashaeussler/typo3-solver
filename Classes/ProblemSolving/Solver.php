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

namespace EliasHaeussler\Typo3Solver\ProblemSolving;

use EliasHaeussler\Typo3Solver\Cache;
use EliasHaeussler\Typo3Solver\Configuration;
use EliasHaeussler\Typo3Solver\Exception;
use EliasHaeussler\Typo3Solver\Formatter;
use Throwable;

/**
 * Solver
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
final class Solver
{
    private readonly Solution\Provider\CacheSolutionProvider $provider;
    private readonly Configuration\Configuration $configuration;

    public function __construct(
        Solution\Provider\SolutionProvider $provider,
        private readonly Formatter\Formatter $formatter,
    ) {
        $this->provider = new Solution\Provider\CacheSolutionProvider(new Cache\SolutionsCache(), $provider);
        $this->configuration = new Configuration\Configuration();
    }

    /**
     * @throws Exception\UnableToSolveException
     */
    public function solve(Throwable $exception): ?string
    {
        $problem = new Problem\Problem(
            $exception,
            $this->provider->getProvider(),
            $this->configuration->getPrompt()->generate($exception),
        );

        if (!$this->provider->canBeUsed($problem)) {
            return null;
        }

        $solution = $this->provider->getSolution($problem);

        return $this->formatter->format($solution);
    }
}
