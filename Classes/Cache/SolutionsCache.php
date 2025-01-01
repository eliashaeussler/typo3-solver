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

namespace EliasHaeussler\Typo3Solver\Cache;

use EliasHaeussler\Typo3Solver\Configuration;
use EliasHaeussler\Typo3Solver\ProblemSolving;
use Symfony\Component\Filesystem;
use TYPO3\CMS\Core;

/**
 * SolutionsCache.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 *
 * @phpstan-import-type SolutionArray from Serializer\SolutionSerializer
 */
final class SolutionsCache
{
    private readonly Configuration\Configuration $configuration;
    private readonly Filesystem\Filesystem $filesystem;
    private readonly Serializer\SolutionSerializer $serializer;
    private string $cachePath;

    public function __construct()
    {
        $this->configuration = new Configuration\Configuration();
        $this->filesystem = new Filesystem\Filesystem();
        $this->serializer = new Serializer\SolutionSerializer();

        $this->initializeCache();
    }

    public function get(ProblemSolving\Problem\Problem $problem): ?ProblemSolving\Solution\Solution
    {
        // Early return if cache is disabled
        if (!$problem->getSolutionProvider()->isCacheable() || !$this->configuration->isCacheEnabled()) {
            return null;
        }

        /** @phpstan-var array{solutions: array<string, SolutionArray>} $cacheData */
        $cacheData = require $this->cachePath;
        $entryIdentifier = $this->calculateCacheIdentifier($problem);

        if (!\is_array($cacheData['solutions'][$entryIdentifier] ?? null)) {
            return null;
        }

        $solution = $this->serializer->deserialize($cacheData['solutions'][$entryIdentifier]);

        // Early return if solution is expired
        if ($solution === null) {
            $this->remove($entryIdentifier);

            return null;
        }

        return $solution->setCacheIdentifier($entryIdentifier);
    }

    public function set(ProblemSolving\Problem\Problem $problem, ProblemSolving\Solution\Solution $solution): void
    {
        // Early return if cache is disabled
        if (!$problem->getSolutionProvider()->isCacheable() || !$this->configuration->isCacheEnabled()) {
            return;
        }

        $cacheData = require $this->cachePath;
        $entryIdentifier = $this->calculateCacheIdentifier($problem);
        $cacheData['solutions'][$entryIdentifier] = $this->serializer->serialize($solution);

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
            \implode('-', [
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
            Core\Utility\GeneralUtility::mkdir_deep(\dirname($this->cachePath));
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
            \sprintf('<?php return %s;', \var_export($cacheData, true)),
        );
    }
}
