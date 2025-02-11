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

namespace EliasHaeussler\Typo3Solver\ProblemSolving\Solution\Prompt;

use EliasHaeussler\Typo3Solver\View;
use Spatie\Backtrace;
use TYPO3\CMS\Core;

/**
 * DefaultPrompt.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
final class DefaultPrompt implements Prompt
{
    private readonly Core\Information\Typo3Version $typo3Version;

    public function __construct(
        private readonly View\TemplateRenderer $renderer,
        private readonly Core\Database\ConnectionPool $connectionPool,
    ) {
        $this->typo3Version = new Core\Information\Typo3Version();
    }

    public static function create(): static
    {
        return new self(new View\TemplateRenderer(), new Core\Database\ConnectionPool());
    }

    public function generate(\Throwable $exception): string
    {
        return \trim(
            $this->renderer->render('Prompt/Default', [
                'exception' => $exception,
                'exceptionClass' => $exception::class,
                'snippet' => $this->createCodeSnippet($exception),
                'mode' => Core\Core\Environment::isComposerMode() ? 'composer-managed' : 'classic mode',
                'typo3Version' => $this->typo3Version->getVersion(),
                'phpVersion' => PHP_VERSION,
                'dbVersion' => $this->getDatabasePlatformAndVersion(),
            ]),
        );
    }

    private function createCodeSnippet(\Throwable $exception): string
    {
        $backtrace = Backtrace\Backtrace::createForThrowable($exception);
        $frames = $backtrace->frames();
        $applicationFrame = $frames[$backtrace->firstApplicationFrameIndex()] ?? null;
        $snippet = '';

        // Early return if application frame could not be determined
        if ($applicationFrame === null) {
            return $snippet;
        }

        /**
         * @var int $number
         * @var string $line
         */
        foreach ($applicationFrame->getSnippet(9) as $number => $line) {
            $snippet .= $number . ' ' . $line . PHP_EOL;
        }

        return \trim($snippet);
    }

    private function getDatabasePlatformAndVersion(): ?string
    {
        try {
            $connection = $this->connectionPool->getConnectionByName(
                Core\Database\ConnectionPool::DEFAULT_CONNECTION_NAME,
            );
        } catch (\Exception) {
            return null;
        }

        return $connection->getServerVersion();
    }
}
