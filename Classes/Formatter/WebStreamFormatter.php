<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS extension "solver".
 *
 * Copyright (C) 2024 Elias Häußler <elias@haeussler.dev>
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

namespace EliasHaeussler\Typo3Solver\Formatter;

use EliasHaeussler\Typo3Solver\ProblemSolving;
use EliasHaeussler\Typo3Solver\View;
use OpenAI\Responses;

use function array_filter;
use function count;
use function json_encode;

/**
 * WebStreamFormatter.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 *
 * @internal
 */
final class WebStreamFormatter implements Formatter
{
    public function __construct(
        private readonly View\TemplateRenderer $renderer,
    ) {}

    public function format(
        ProblemSolving\Problem\Problem $problem,
        ProblemSolving\Solution\Solution $solution,
    ): string {
        $json = [
            'data' => [
                'model' => $solution->getModel(),
                'numberOfChoices' => count($solution->getChoices()),
                'numberOfPendingChoices' => count($this->filterPendingChoices($solution->getChoices())),
                'prompt' => $solution->getPrompt(),
            ],
            'content' => $this->renderer->render('Solution/WebStream', [
                'solution' => $solution,
            ]),
        ];

        return json_encode($json, JSON_THROW_ON_ERROR);
    }

    /**
     * @param list<Responses\Chat\CreateResponseChoice> $choices
     * @return array<Responses\Chat\CreateResponseChoice>
     */
    private function filterPendingChoices(array $choices): array
    {
        return array_filter(
            $choices,
            static fn(Responses\Chat\CreateResponseChoice $choice): bool => $choice->finishReason === null,
        );
    }
}
