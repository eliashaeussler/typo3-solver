<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS extension "solver".
 *
 * Copyright (C) 2023-2024 Elias Häußler <elias@haeussler.dev>
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

use Symfony\Component\Filesystem;
use TYPO3\CMS\Core;

/**
 * ExceptionsCache
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 *
 * @phpstan-import-type ExceptionArray from Serializer\ExceptionSerializer
 */
final class ExceptionsCache
{
    private readonly Filesystem\Filesystem $filesystem;
    private string $cachePath;
    private readonly Serializer\ExceptionSerializer $serializer;

    public function __construct()
    {
        $this->filesystem = new Filesystem\Filesystem();
        $this->serializer = new Serializer\ExceptionSerializer();

        $this->initializeCache();
    }

    public function get(string $entryIdentifier): ?\Throwable
    {
        /** @var array{exceptions: array<string, ExceptionArray>} $cacheData */
        $cacheData = require $this->cachePath;

        if (!is_array($cacheData['exceptions'][$entryIdentifier] ?? null)) {
            return null;
        }

        return $this->serializer->deserialize($cacheData['exceptions'][$entryIdentifier]);
    }

    public function getIdentifier(\Throwable $exception): string
    {
        return $this->calculateCacheIdentifier($exception);
    }

    public function set(\Throwable $exception): string
    {
        $cacheData = require $this->cachePath;
        $entryIdentifier = $this->calculateCacheIdentifier($exception);
        $cacheData['exceptions'][$entryIdentifier] = $this->serializer->serialize($exception);

        $this->write($cacheData);

        return $entryIdentifier;
    }

    public function flush(): void
    {
        $this->write([]);
    }

    /**
     * @internal
     */
    public function remove(\Throwable|string $entry): void
    {
        $cacheData = require $this->cachePath;

        if ($entry instanceof \Throwable) {
            $entry = $this->calculateCacheIdentifier($entry);
        }

        unset($cacheData['exceptions'][$entry]);

        $this->write($cacheData);
    }

    private function calculateCacheIdentifier(\Throwable $exception): string
    {
        return sha1(
            implode('-', [
                $exception->getMessage(),
                $exception->getCode(),
                $exception->getFile(),
                $exception->getLine(),
                $exception->getPrevious(),
            ]),
        );
    }

    private function initializeCache(): void
    {
        $this->cachePath = Filesystem\Path::join(
            Core\Core\Environment::getVarPath(),
            'cache/data/tx_solver/exceptions.php',
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
