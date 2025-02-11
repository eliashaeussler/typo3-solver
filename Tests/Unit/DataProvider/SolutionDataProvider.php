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

namespace EliasHaeussler\Typo3Solver\Tests\Unit\DataProvider;

use EliasHaeussler\Typo3Solver\ProblemSolving;

/**
 * SolutionDataProvider
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 * @internal
 */
final class SolutionDataProvider
{
    public static function get(int $numberOfResponses = 1, string $message = 'message {index}'): ProblemSolving\Solution\Solution
    {
        $responses = [];

        for ($i = 0; $i < $numberOfResponses; ++$i) {
            $responses[] = self::getResponse(\str_replace('{index}', (string)($i + 1), $message), $i);
        }

        return new ProblemSolving\Solution\Solution($responses, 'model', 'prompt');
    }

    /**
     * @return \Traversable<ProblemSolving\Solution\Solution>
     */
    public static function getStream(int $numberOfDeltas = 2, int $numberOfResponses = 1): \Traversable
    {
        for ($i = 0; $i < $numberOfDeltas; ++$i) {
            $message = 'message {index}';

            if ($i > 0) {
                $message = ' ... ' . $message;
            }

            yield self::get($numberOfResponses, $message);
        }
    }

    public static function getResponse(string $message, int $index = 0): ProblemSolving\Solution\Model\CompletionResponse
    {
        return new ProblemSolving\Solution\Model\CompletionResponse(
            $index,
            new ProblemSolving\Solution\Model\Message('role', $message),
        );
    }
}
