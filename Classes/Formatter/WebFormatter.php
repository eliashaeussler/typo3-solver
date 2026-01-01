<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS extension "solver".
 *
 * Copyright (C) 2023-2026 Elias Häußler <elias@haeussler.dev>
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

namespace EliasHaeussler\Typo3Solver\Formatter;

use EliasHaeussler\Typo3Solver\Authentication;
use EliasHaeussler\Typo3Solver\Cache;
use EliasHaeussler\Typo3Solver\ProblemSolving;
use EliasHaeussler\Typo3Solver\View;
use Symfony\Component\Filesystem;

/**
 * WebFormatter.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
final readonly class WebFormatter implements Formatter
{
    private const SCRIPT_PATH = 'Resources/Public/JavaScript/main.js';
    private const STYLESHEET_PATH = 'Resources/Public/Css/main.css';

    public function __construct(
        private Cache\ExceptionsCache $exceptionsCache,
        private View\TemplateRenderer $renderer,
        private Authentication\StreamAuthentication $authentication,
    ) {}

    public function format(
        ProblemSolving\Problem\Problem $problem,
        ProblemSolving\Solution\Solution $solution,
    ): string {
        return $this->renderer->render('Solution/Web', [
            'solution' => $solution,
            'exceptionCacheIdentifier' => $this->exceptionsCache->getIdentifier($problem->getException()),
            'streamHash' => $this->authentication->register(),
        ]);
    }

    public function getAdditionalStyles(): string
    {
        return $this->getFileContents(self::STYLESHEET_PATH);
    }

    public function getAdditionalScripts(): string
    {
        return '<script>' . $this->getFileContents(self::SCRIPT_PATH) . '</script>';
    }

    private function getFileContents(string $filename): string
    {
        $rootPath = \dirname(__DIR__, 2);
        $filePath = Filesystem\Path::join($rootPath, $filename);

        if (\file_exists($filePath)) {
            return (string)@\file_get_contents($filePath);
        }

        return ''; // @codeCoverageIgnore
    }
}
