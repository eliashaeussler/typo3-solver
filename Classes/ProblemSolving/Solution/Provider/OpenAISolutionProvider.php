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
use Throwable;
use Traversable;

use function in_array;

final class OpenAISolutionProvider implements StreamedSolutionProvider
{
    private readonly Configuration\Configuration $configuration;
    private readonly OpenAI\Client $client;

    public function __construct(
        OpenAI\Client $client = null,
    ) {
        $this->configuration = new Configuration\Configuration();
        $this->client = $client ?? OpenAI::client($this->configuration->getApiKey() ?? '');
    }

    /**
     * @throws Exception\ApiKeyMissingException
     * @throws Exception\UnableToSolveException
     */
    public function getSolution(ProblemSolving\Problem\Problem $problem): ProblemSolving\Solution\Solution
    {
        if ($this->configuration->getApiKey() === null) {
            throw Exception\ApiKeyMissingException::create();
        }

        try {
            $response = $this->client->chat()->create($this->buildParameters($problem));
        } catch (\Exception) {
            throw Exception\UnableToSolveException::create($problem);
        }

        return ProblemSolving\Solution\Solution::fromResponse($response, $problem->getPrompt());
    }

    /**
     * @throws Exception\ApiKeyMissingException
     */
    public function getStreamedSolution(ProblemSolving\Problem\Problem $problem): Traversable
    {
        if ($this->configuration->getApiKey() === null) {
            throw Exception\ApiKeyMissingException::create();
        }

        try {
            $stream = $this->client->chat()->createStreamed($this->buildParameters($problem));
        } catch (\Exception) {
            throw Exception\UnableToSolveException::create($problem);
        }

        yield from ProblemSolving\Solution\Solution::fromStream($stream, $problem->getPrompt());
    }

    public function canBeUsed(Throwable $exception): bool
    {
        return !in_array($exception->getCode(), $this->configuration->getIgnoredCodes(), true);
    }

    public function isCacheable(): bool
    {
        return true;
    }

    /**
     * @return array{
     *     model: string,
     *     messages: list<array{role: string, content: string}>,
     *     max_tokens: int,
     *     temperature: float,
     *     n: int,
     * }
     */
    private function buildParameters(ProblemSolving\Problem\Problem $problem): array
    {
        return [
            'model' => $this->configuration->getModel(),
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $problem->getPrompt(),
                ],
            ],
            'max_tokens' => $this->configuration->getMaxTokens(),
            'temperature' => $this->configuration->getTemperature(),
            'n' => $this->configuration->getNumberOfCompletions(),
        ];
    }
}
