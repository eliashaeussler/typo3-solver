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
use GeminiAPI\Client;
use GeminiAPI\Enums;
use GeminiAPI\GenerationConfig;
use GeminiAPI\GenerativeModel;
use GeminiAPI\Resources;
use GeminiAPI\Responses;

/**
 * GeminiSolutionProvider
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
final class GeminiSolutionProvider implements StreamedSolutionProvider
{
    private readonly GenerativeModel $model;
    private readonly string $modelName;

    public function __construct(
        private readonly Configuration\Configuration $configuration,
        private readonly Client $client,
    ) {
        $this->model = $this->createModel();
        $this->modelName = $this->model->modelName instanceof Enums\ModelName
            ? $this->model->modelName->value
            : $this->model->modelName
        ;
    }

    /**
     * @throws Exception\ApiKeyMissingException
     */
    public static function create(Client $client = null): static
    {
        $configuration = new Configuration\Configuration();
        $client ??= (new Http\ClientFactory($configuration))->getGeminiClient();

        return new self($configuration, $client);
    }

    public function getSolution(ProblemSolving\Problem\Problem $problem): ProblemSolving\Solution\Solution
    {
        if ($this->configuration->getApiKey() === null) {
            throw Exception\ApiKeyMissingException::create();
        }

        try {
            $response = $this->model->generateContent(new Resources\Parts\TextPart($problem->getPrompt()));
        } catch (\Exception $exception) {
            throw Exception\UnableToSolveException::create($problem, $exception);
        }

        return ProblemSolving\Solution\Solution::fromGeminiResponse($response, $this->modelName, $problem->getPrompt());
    }

    public function getStreamedSolution(ProblemSolving\Problem\Problem $problem): \Traversable
    {
        if ($this->configuration->getApiKey() === null) {
            throw Exception\ApiKeyMissingException::create();
        }

        // Store all responses in array to merge them during streaming
        $responses = [];

        $fiber = new \Fiber(
            function () use ($problem) {
                // Suspend immediately to receive first response in while loop
                \Fiber::suspend();

                $this->model->generateContentStream(
                    static function (Responses\GenerateContentResponse $response) {
                        \Fiber::suspend($response);
                    },
                    [
                        new Resources\Parts\TextPart($problem->getPrompt()),
                    ],
                );
            },
        );
        $fiber->start();

        // Loop over each streamed response
        while (!$fiber->isTerminated()) {
            /** @var Responses\GenerateContentResponse|null $response */
            $response = $fiber->resume();

            if ($response !== null) {
                foreach ($response->candidates as $candidate) {
                    $completionResponse = ProblemSolving\Solution\Model\CompletionResponse::fromGeminiCandidate($candidate);

                    // Merge previous responses with currently streamed solution candidate
                    if (isset($responses[$candidate->index])) {
                        $completionResponse = $responses[$candidate->index]->merge($completionResponse);
                    }

                    $responses[$candidate->index] = $completionResponse;
                }

                // Yield solution with merged responses
                yield new ProblemSolving\Solution\Solution($responses, $this->modelName, $problem->getPrompt());
            }
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
        $models = $this->client->listModels()->models;

        // Filter by supported models
        if (!$includeUnsupported) {
            $models = \array_filter($models, $this->isSupportedModel(...));
        }

        return \array_values(
            \array_map(
                static fn(Resources\Model $model) => Model\AiModel::fromGeminiModel($model),
                $models,
            ),
        );
    }

    /**
     * @see https://ai.google.dev/api/models#Model.FIELDS.supported_generation_methods
     * @see https://ai.google.dev/gemini-api/docs/models/gemini#model-variations
     */
    private function isSupportedModel(Resources\Model $model): bool
    {
        return \in_array('generateContent', $model->supportedGenerationMethods, true);
    }

    private function createModel(): GenerativeModel
    {
        $config = (new GenerationConfig())
            ->withCandidateCount($this->configuration->getNumberOfCompletions())
            ->withTemperature($this->configuration->getTemperature())
            ->withMaxOutputTokens($this->configuration->getMaxTokens())
        ;

        return $this->client->generativeModel($this->configuration->getModel())
            ->withGenerationConfig($config)
        ;
    }
}
