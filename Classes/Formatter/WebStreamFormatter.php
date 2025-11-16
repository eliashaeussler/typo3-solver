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

namespace EliasHaeussler\Typo3Solver\Formatter;

use EliasHaeussler\Typo3Solver\ProblemSolving;
use EliasHaeussler\Typo3Solver\View;

/**
 * WebStreamFormatter.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 *
 * @internal
 */
final readonly class WebStreamFormatter implements Formatter
{
    public function __construct(
        private View\TemplateRenderer $renderer,
    ) {}

    public function format(
        ProblemSolving\Problem\Problem $problem,
        ProblemSolving\Solution\Solution $solution,
    ): string {
        $json = [
            'data' => [
                'model' => $solution->model,
                'numberOfResponses' => \count($solution->responses),
                'numberOfPendingResponses' => $this->countPendingResponses($solution->responses),
                'prompt' => $solution->prompt,
            ],
            'content' => $this->renderer->render('Solution/WebStream', [
                'solution' => $solution,
            ]),
        ];

        return \json_encode($json, JSON_THROW_ON_ERROR);
    }

    /**
     * @param array<int, ProblemSolving\Solution\Model\CompletionResponse> $responses
     */
    private function countPendingResponses(array $responses): int
    {
        $pendingResponses = \array_filter(
            $responses,
            static fn(ProblemSolving\Solution\Model\CompletionResponse $response): bool => !$response->isFinished(),
        );

        return \count($pendingResponses);
    }
}
