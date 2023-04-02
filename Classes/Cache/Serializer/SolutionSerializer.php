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

namespace EliasHaeussler\Typo3Solver\Cache\Serializer;

use DateTimeImmutable;
use EliasHaeussler\Typo3Solver\Configuration;
use EliasHaeussler\Typo3Solver\ProblemSolving;

/**
 * SolutionSerializer
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 *
 * @internal
 *
 * @phpstan-import-type SolutionArray from ProblemSolving\Solution\Solution as SolutionSubArray
 * @phpstan-type SolutionArray array{
 *     solution: SolutionSubArray,
 *     createdAt: int,
 *     validUntil: int,
 * }
 */
final class SolutionSerializer
{
    private readonly Configuration\Configuration $configuration;

    public function __construct()
    {
        $this->configuration = new Configuration\Configuration();
    }

    /**
     * @phpstan-return SolutionArray
     */
    public function serialize(ProblemSolving\Solution\Solution $solution): array
    {
        return [
            'solution' => $solution->toArray(),
            'createdAt' => time(),
            'validUntil' => time() + $this->configuration->getCacheLifetime(),
        ];
    }

    /**
     * @phpstan-param SolutionArray $solutionArray
     */
    public function deserialize(array $solutionArray): ?ProblemSolving\Solution\Solution
    {
        [
            'solution' => $solution,
            'createdAt' => $createdAt,
            'validUntil' => $validUntil,
        ] = $solutionArray;

        if ($validUntil < time()) {
            return null;
        }

        return ProblemSolving\Solution\Solution::fromArray($solution)
            ->setCreateDate(new DateTimeImmutable('@' . $createdAt))
        ;
    }
}
