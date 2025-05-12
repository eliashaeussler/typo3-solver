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

use Anthropic\Client;
use Anthropic\Contracts;
use Anthropic\Responses;
use Anthropic\ValueObjects;
use EliasHaeussler\Typo3Solver\Configuration;
use EliasHaeussler\Typo3Solver\Exception;
use EliasHaeussler\Typo3Solver\Http;
use EliasHaeussler\Typo3Solver\ProblemSolving;

/**
 * ClaudeSolutionProvider
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
final class ClaudeSolutionProvider implements StreamedSolutionProvider
{
    public function __construct(
        private readonly Configuration\Configuration $configuration,
        private readonly Client $client,
    ) {}

    /**
     * @throws Exception\ApiKeyMissingException
     */
    public static function create(Client $client = null): static
    {
        $configuration = new Configuration\Configuration();
        $client ??= (new Http\ClientFactory($configuration))->getAnthropicClient();

        return new self($configuration, $client);
    }

    public function getSolution(ProblemSolving\Problem\Problem $problem): ProblemSolving\Solution\Solution
    {
        if ($this->configuration->getApiKey() === null) {
            throw Exception\ApiKeyMissingException::create();
        }

        try {
            $response = $this->client->messages()->create($this->buildParameters($problem));
        } catch (\Exception $exception) {
            throw Exception\UnableToSolveException::create($problem, $exception);
        }

        return ProblemSolving\Solution\Solution::fromAnthropicResponse($response, $problem->getPrompt());
    }

    public function getStreamedSolution(ProblemSolving\Problem\Problem $problem): \Traversable
    {
        if ($this->configuration->getApiKey() === null) {
            throw Exception\ApiKeyMissingException::create();
        }

        // Create solution stream
        try {
            $stream = $this->client->messages()->createStreamed($this->buildParameters($problem));
        } catch (\Exception $exception) {
            throw Exception\UnableToSolveException::create($problem, $exception);
        }

        // Store all responses in array to merge them during streaming
        $responses = [];
        $model = null;

        /** @var Responses\Messages\CreateStreamedResponse $response */
        foreach ($stream as $response) {
            $index = 0;
            $model = $response->message->model ?? $model;
            $type = $response->content_block_start->type ?? $response->delta->type ?? 'text';
            $text = $response->content_block_start->text ?? $response->delta->text;
            $stopReason = $response->delta->stop_reason;

            if ($text === null) {
                continue;
            }

            $completionResponse = ProblemSolving\Solution\Model\CompletionResponse::fromAnthropicContent(
                $index,
                Responses\Messages\CreateResponseContent::from(['type' => $type, 'text' => $text]),
                $stopReason,
            );

            // Merge previous responses with currently streamed solution choices
            if (isset($responses[$index])) {
                $completionResponse = $responses[$index]->merge($completionResponse);
            }

            $responses[$index] = $completionResponse;

            // Yield solution with merged responses
            yield new ProblemSolving\Solution\Solution($responses, $model ?? $this->configuration->getModel(), $problem->getPrompt());
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

    /**
     * @see https://docs.anthropic.com/en/api/models-list
     * @see https://docs.anthropic.com/en/docs/about-claude/models/all-models
     */
    public function listModels(bool $includeUnsupported = false): array
    {
        // Retrieve all available models
        $payload = ValueObjects\Transporter\Payload::list('models', ['limit' => 200]);
        $transporter = $this->extractTransporterFromClient();
        $response = $transporter->requestObject($payload);
        $data = $response->data();

        // Early return on unexpected response data
        if (!\is_array($data)) {
            return [];
        }

        /** @var array<array{created_at: string, display_name: string, id: string, type: string}> $models */
        $models = $data['data'] ?? [];

        return \array_values(
            \array_map(
                static fn(array $model) => Model\AiModel::fromAnthropicModel($model),
                $models,
            ),
        );
    }

    /**
     * @return array{
     *     model: string,
     *     max_tokens: int,
     *     temperature: float,
     *     messages: list<array{role: string, content: string}>,
     * }
     */
    private function buildParameters(ProblemSolving\Problem\Problem $problem): array
    {
        return [
            'model' => $this->configuration->getModel(),
            'max_tokens' => $this->configuration->getMaxTokens(),
            'temperature' => $this->configuration->getTemperature(),
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $problem->getPrompt(),
                ],
            ],
        ];
    }

    private function extractTransporterFromClient(): Contracts\TransporterContract
    {
        $reflection = new \ReflectionObject($this->client);
        /** @var Contracts\TransporterContract $transporter */
        $transporter = $reflection->getProperty('transporter')->getValue($this->client);

        return $transporter;
    }
}
