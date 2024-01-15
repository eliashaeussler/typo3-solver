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

namespace EliasHaeussler\Typo3Solver\Tests\Unit\DataProvider;

use EliasHaeussler\Typo3Solver\ProblemSolving;
use OpenAI\Responses;
use Traversable;

use function str_replace;

/**
 * SolutionDataProvider
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 * @internal
 */
final class SolutionDataProvider
{
    public static function get(int $numberOfChoices = 1, string $message = 'message {index}'): ProblemSolving\Solution\Solution
    {
        $choices = [];

        for ($i = 0; $i < $numberOfChoices; ++$i) {
            $choices[] = self::getChoice(str_replace('{index}', (string)($i + 1), $message), $i);
        }

        return new ProblemSolving\Solution\Solution($choices, 'model', 'prompt');
    }

    /**
     * @return Traversable<ProblemSolving\Solution\Solution>
     */
    public static function getStream(int $numberOfDeltas = 2, int $numberOfChoices = 1): Traversable
    {
        for ($i = 0; $i < $numberOfDeltas; ++$i) {
            $message = 'message {index}';

            if ($i > 0) {
                $message = ' ... ' . $message;
            }

            yield self::get($numberOfChoices, $message);
        }
    }

    public static function getChoice(string $message, int $index = 0): Responses\Chat\CreateResponseChoice
    {
        return Responses\Chat\CreateResponseChoice::from([
            'index' => $index,
            'message' => [
                'role' => 'role',
                'content' => $message,
                'function_call' => null,
                'tool_calls' => null,
            ],
            'finish_reason' => null,
        ]);
    }
}
