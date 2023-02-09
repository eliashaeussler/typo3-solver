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

namespace EliasHaeussler\Typo3Solver\ProblemSolving\Solution\Provider;

use EliasHaeussler\Typo3Solver\Configuration;
use EliasHaeussler\Typo3Solver\Exception;
use EliasHaeussler\Typo3Solver\ProblemSolving;
use OpenAI;

use function in_array;

final class OpenAISolutionProvider implements SolutionProvider
{
    private readonly Configuration\Configuration $configuration;
    private readonly OpenAI\Client $client;

    public function __construct(
        OpenAI\Client $client = null,
    ) {
        $this->configuration = new Configuration\Configuration();
        $this->client = $client ?? OpenAI::client($this->configuration->getApiKey() ?? '');
    }

    public function getSolution(ProblemSolving\Problem\Problem $problem): ProblemSolving\Solution\Solution
    {
        try {
            $response = $this->client->completions()->create([
                'model' => $this->configuration->getModel(),
                'prompt' => $problem->getPrompt(),
                'max_tokens' => $this->configuration->getMaxTokens(),
                'temperature' => $this->configuration->getTemperature(),
                'n' => $this->configuration->getNumberOfCompletions(),
            ]);
        } catch (\Exception) {
            throw Exception\UnableToSolveException::create($problem);
        }

        return ProblemSolving\Solution\Solution::fromResponse($response, $problem->getPrompt());
    }

    public function canBeUsed(ProblemSolving\Problem\Problem $problem): bool
    {
        return $this->configuration->getApiKey() !== null
            && !in_array($problem->getException()->getCode(), $this->configuration->getIgnoredCodes(), true);
    }
}
