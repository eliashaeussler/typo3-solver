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

namespace EliasHaeussler\Typo3Solver\ProblemSolving\Solution\Prompt;

use EliasHaeussler\Typo3Solver\View;
use Spatie\Backtrace;
use Throwable;
use TYPO3\CMS\Core;

use function trim;

/**
 * DefaultPrompt.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
final class DefaultPrompt implements Prompt
{
    private readonly Core\Information\Typo3Version $typo3Version;
    private readonly View\TemplateRenderer $renderer;

    public function __construct()
    {
        $this->typo3Version = new Core\Information\Typo3Version();
        $this->renderer = new View\TemplateRenderer();
    }

    public function generate(Throwable $exception): string
    {
        $prompt = $this->renderer->render('Prompt/Default.html', [
            'exception' => $exception,
            'snippet' => $this->createCodeSnippet($exception),
            'mode' => Core\Core\Environment::isComposerMode() ? 'composer' : 'classic (symlink)',
            'typo3Version' => $this->typo3Version->getVersion(),
        ]);

        return trim($prompt);
    }

    private function createCodeSnippet(Throwable $exception): string
    {
        $backtrace = Backtrace\Backtrace::createForThrowable($exception);
        $frames = $backtrace->frames();
        $applicationFrame = $frames[$backtrace->firstApplicationFrameIndex()] ?? null;
        $snippet = '';

        // Early return if application frame could not be determined
        if ($applicationFrame === null) {
            return $snippet;
        }

        foreach ($applicationFrame->getSnippet(9) as $number => $line) {
            $snippet .= $number . ' ' . $line . PHP_EOL;
        }

        return $snippet;
    }
}
