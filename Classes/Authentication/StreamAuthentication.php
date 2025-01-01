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

namespace EliasHaeussler\Typo3Solver\Authentication;

use EliasHaeussler\Typo3Solver\Exception;
use Symfony\Component\Filesystem;
use TYPO3\CMS\Core;

use function file;

/**
 * StreamAuthentication
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 *
 * @internal
 */
final class StreamAuthentication
{
    private string $storagePath;

    public function __construct()
    {
        $this->initializeStorage();
    }

    /**
     * @throws Exception\AuthenticationFailureException
     */
    public function authenticate(string $hash): void
    {
        $registeredHashes = @\file($this->storagePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        // Throw exception if file cannot be read
        if ($registeredHashes === false) {
            throw Exception\AuthenticationFailureException::create();
        }

        // Throw exception if hash is not registered
        if (($line = \array_search($hash, $registeredHashes, true)) === false) {
            throw Exception\AuthenticationFailureException::create();
        }

        // Remove hash
        unset($registeredHashes[$line]);

        $this->write($registeredHashes);
    }

    public function register(): string
    {
        $hash = \sha1(\uniqid('tx_solver_stream_hash_'));

        $this->write([$hash], true);

        return $hash;
    }

    private function initializeStorage(): void
    {
        $this->storagePath = Filesystem\Path::join(
            Core\Core\Environment::getVarPath(),
            'transient/tx_solver/stream_auth.txt',
        );

        if (!\file_exists($this->storagePath)) {
            Core\Utility\GeneralUtility::mkdir_deep(dirname($this->storagePath));
            $this->write([]);
        }
    }

    /**
     * @param array<string> $hashes
     */
    private function write(array $hashes, bool $append = false): void
    {
        if ($append) {
            \array_unshift($hashes, '');
        }

        $content = \implode(PHP_EOL, $hashes);

        \file_put_contents($this->storagePath, $content, $append ? FILE_APPEND : LOCK_EX);
    }
}
