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

namespace EliasHaeussler\Typo3Solver\Error;

use EliasHaeussler\Typo3Solver\Configuration;
use EliasHaeussler\Typo3Solver\Exception;
use EliasHaeussler\Typo3Solver\Formatter;
use EliasHaeussler\Typo3Solver\ProblemSolving;
use EliasHaeussler\Typo3Solver\Utility;
use EliasHaeussler\Typo3Solver\View;
use Throwable;
use TYPO3\CMS\Core;

/**
 * AiSolverExceptionHandler.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
final class AiSolverExceptionHandler extends Core\Error\DebugExceptionHandler
{
    private readonly Configuration\Configuration $configuration;
    private readonly Formatter\WebFormatter $webFormatter;
    private readonly View\TemplateRenderer $renderer;

    public function __construct()
    {
        parent::__construct();

        $this->configuration = new Configuration\Configuration();
        $this->webFormatter = new Formatter\WebFormatter();
        $this->renderer = new View\TemplateRenderer();
    }

    public function echoExceptionCLI(Throwable $exception): void
    {
        try {
            $solver = new ProblemSolving\Solver($this->configuration->getProvider(), new Formatter\CliFormatter());
            $solution = $solver->solve($exception);

            if ($solution !== null) {
                echo $solution;
            }
        } catch (Exception\UnableToSolveException) {
            // Intended fallthrough.
        }

        parent::echoExceptionCLI($exception);
    }

    protected function getContent(Throwable $throwable): string
    {
        $content = parent::getContent($throwable);

        try {
            $solver = new ProblemSolving\Solver($this->configuration->getProvider(), $this->webFormatter);
            $solution = $solver->solve($throwable);
        } catch (\Exception $exception) {
            $solution = $this->renderException($exception);
        }

        if ($solution === null) {
            return $content;
        }

        return Utility\StringUtility::replaceFirstOccurrence(
            '<div class="trace">',
            $solution . '<div class="trace">',
            $content,
        );
    }

    protected function getStylesheet(): string
    {
        return parent::getStylesheet() . $this->webFormatter->getAdditionalStyles();
    }

    private function renderException(\Exception $exception): string
    {
        return $this->renderer->render('Message/Exception.html', [
            'exception' => $exception,
            'exceptionClass' => $exception::class,
        ]);
    }
}
