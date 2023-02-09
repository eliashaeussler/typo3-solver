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

namespace EliasHaeussler\Typo3Solver\Cache;

use DateTimeImmutable;
use EliasHaeussler\Typo3Solver\Configuration;
use EliasHaeussler\Typo3Solver\ProblemSolving;
use Symfony\Component\Filesystem;
use TYPO3\CMS\Core;

use function dirname;
use function implode;
use function is_array;
use function sprintf;
use function time;
use function var_export;

/**
 * SolutionsCache.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 *
 * @phpstan-import-type SolutionArray from ProblemSolving\Solution\Solution
 */
final class SolutionsCache
{
    private readonly Filesystem\Filesystem $filesystem;
    private string $cachePath;
    private readonly Configuration\Configuration $configuration;

    public function __construct()
    {
        $this->filesystem = new Filesystem\Filesystem();
        $this->configuration = new Configuration\Configuration();

        $this->initializeCache();
    }

    public function get(ProblemSolving\Problem\Problem $problem): ?ProblemSolving\Solution\Solution
    {
        /** @phpstan-var array{solutions: array<string, array{solution: SolutionArray, createdAt: int, validUntil: int}>} $cacheData */
        $cacheData = require $this->cachePath;
        $entryIdentifier = $this->calculateCacheIdentifier($problem);

        if (!is_array($cacheData['solutions'][$entryIdentifier] ?? null)) {
            return null;
        }

        [
            'solution' => $solution,
            'createdAt' => $createdAt,
            'validUntil' => $validUntil,
        ] = $cacheData['solutions'][$entryIdentifier];

        if ($validUntil < time()) {
            $this->remove($entryIdentifier);

            return null;
        }

        return ProblemSolving\Solution\Solution::fromArray($solution)
            ->setCreateDate(new DateTimeImmutable('@' . $createdAt))
            ->setCacheIdentifier($entryIdentifier);
    }

    public function set(ProblemSolving\Problem\Problem $problem, ProblemSolving\Solution\Solution $solution): void
    {
        // Early return if cache is disabled
        if ($this->configuration->getCacheLifetime() === 0) {
            return;
        }

        $cacheData = require $this->cachePath;
        $entryIdentifier = $this->calculateCacheIdentifier($problem);
        $cacheData['solutions'][$entryIdentifier] = [
            'solution' => $solution->toArray(),
            'createdAt' => time(),
            'validUntil' => time() + $this->configuration->getCacheLifetime(),
        ];

        $this->write($cacheData);
    }

    public function flush(): void
    {
        $this->write([]);
    }

    /**
     * @internal
     */
    public function remove(ProblemSolving\Problem\Problem|string $entry): void
    {
        $cacheData = require $this->cachePath;

        if ($entry instanceof ProblemSolving\Problem\Problem) {
            $entry = $this->calculateCacheIdentifier($entry);
        }

        unset($cacheData['solutions'][$entry]);

        $this->write($cacheData);
    }

    private function calculateCacheIdentifier(ProblemSolving\Problem\Problem $problem): string
    {
        return sha1(
            implode('-', [
                $problem->getSolutionProvider()::class,
                $this->configuration->getModel(),
                $this->configuration->getTemperature(),
                $this->configuration->getMaxTokens(),
                $this->configuration->getNumberOfCompletions(),
                $problem->getPrompt(),
            ]),
        );
    }

    private function initializeCache(): void
    {
        $this->cachePath = Filesystem\Path::join(
            Core\Core\Environment::getVarPath(),
            'cache/data/tx_solver/solutions.php',
        );

        if (!$this->filesystem->exists($this->cachePath)) {
            Core\Utility\GeneralUtility::mkdir_deep(dirname($this->cachePath));
            $this->write([]);
        }
    }

    /**
     * @param array<string, mixed> $cacheData
     */
    private function write(array $cacheData): void
    {
        $this->filesystem->dumpFile(
            $this->cachePath,
            sprintf('<?php return %s;', var_export($cacheData, true)),
        );
    }
}
