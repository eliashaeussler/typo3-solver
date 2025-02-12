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

namespace EliasHaeussler\Typo3Solver\ProblemSolving\Solution\Provider;

use EliasHaeussler\Typo3Solver\Configuration;
use EliasHaeussler\Typo3Solver\Exception;
use EliasHaeussler\Typo3Solver\Http;
use EliasHaeussler\Typo3Solver\ProblemSolving;
use OpenAI\Client;
use OpenAI\Responses;

final class OpenAISolutionProvider implements StreamedSolutionProvider
{
    public function __construct(
        private readonly Configuration\Configuration $configuration,
        private readonly Client $client,
    ) {}

    public static function create(Client $client = null): static
    {
        $configuration = new Configuration\Configuration();
        $client ??= (new Http\ClientFactory($configuration))->getOpenAIClient();

        return new self($configuration, $client);
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
        } catch (\Exception $exception) {
            throw Exception\UnableToSolveException::create($problem, $exception);
        }

        return ProblemSolving\Solution\Solution::fromOpenAIResponse($response, $problem->getPrompt());
    }

    /**
     * @throws Exception\ApiKeyMissingException
     * @throws Exception\UnableToSolveException
     */
    public function getStreamedSolution(ProblemSolving\Problem\Problem $problem): \Traversable
    {
        if ($this->configuration->getApiKey() === null) {
            throw Exception\ApiKeyMissingException::create();
        }

        // Create solution stream
        try {
            $stream = $this->client->chat()->createStreamed($this->buildParameters($problem));
        } catch (\Exception $exception) {
            throw Exception\UnableToSolveException::create($problem, $exception);
        }

        // Store all responses in array to merge them during streaming
        $responses = [];

        /** @var Responses\Chat\CreateStreamedResponse $response */
        foreach ($stream as $response) {
            foreach ($response->choices as $choice) {
                $completionResponse = ProblemSolving\Solution\Model\CompletionResponse::fromOpenAIChoice($choice);

                // Merge previous responses with currently streamed solution choices
                if (isset($responses[$choice->index])) {
                    $completionResponse = $responses[$choice->index]->merge($completionResponse);
                }

                $responses[$choice->index] = $completionResponse;
            }

            // Yield solution with merged responses
            yield new ProblemSolving\Solution\Solution(
                $responses,
                $response->model,
                $problem->getPrompt(),
            );
        }
    }

    public function canBeUsed(\Throwable $exception): bool
    {
        return !\in_array($exception->getCode(), $this->configuration->getIgnoredCodes(), true);
    }

    public function isCacheable(): bool
    {
        return true;
    }

    public function listModels(bool $includeUnsupported = false): array
    {
        // Retrieve all available models
        $modelListResponse = $this->client->models()->list()->data;

        // Filter by supported models
        if (!$includeUnsupported) {
            $modelListResponse = \array_filter($modelListResponse, $this->isSupportedModel(...));
        }

        return \array_values(
            \array_map(
                static fn(Responses\Models\RetrieveResponse $response) => Model\AiModel::fromOpenAIRetrieveResponse($response),
                $modelListResponse,
            ),
        );
    }

    /**
     * @see https://platform.openai.com/docs/models#model-endpoint-compatibility
     */
    private function isSupportedModel(Responses\Models\RetrieveResponse $response): bool
    {
        $identifier = \strtolower($response->id);

        if (!\str_starts_with($identifier, 'gpt-') && !\str_starts_with($identifier, 'chatgpt-')) {
            return false;
        }

        return !\str_contains($identifier, '-realtime') && !\str_contains($identifier, '-audio');
    }

    /**
     * @return array{
     *     model: string,
     *     messages: list<array{role: string, content: string}>,
     *     max_completion_tokens: int,
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
            'max_completion_tokens' => $this->configuration->getMaxTokens(),
            'temperature' => $this->configuration->getTemperature(),
            'n' => $this->configuration->getNumberOfCompletions(),
        ];
    }
}
