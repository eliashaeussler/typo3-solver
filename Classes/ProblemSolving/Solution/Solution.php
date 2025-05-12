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

namespace EliasHaeussler\Typo3Solver\ProblemSolving\Solution;

use Anthropic\Responses as AnthropicResponses;
use EliasHaeussler\Typo3Solver\ProblemSolving;
use IteratorAggregate;
use OpenAI\Responses as OpenAIResponses;

/**
 * Solution.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 *
 * @implements IteratorAggregate<int, Model\CompletionResponse>
 *
 * @phpstan-import-type CompletionResponseArray from ProblemSolving\Solution\Model\CompletionResponse
 * @phpstan-type SolutionArray array{responses: array<int, CompletionResponseArray>, model: string, prompt: string}
 */
final class Solution implements \Countable, \IteratorAggregate, \JsonSerializable
{
    private ?\DateTimeInterface $createDate = null;
    private ?string $cacheIdentifier = null;

    /**
     * @param array<int, Model\CompletionResponse> $responses
     */
    public function __construct(
        public readonly array $responses,
        public readonly string $model,
        public readonly string $prompt,
    ) {}

    public static function fromOpenAIResponse(OpenAIResponses\Chat\CreateResponse $response, string $prompt): self
    {
        return new self(
            \array_map(
                Model\CompletionResponse::fromOpenAIChoice(...),
                $response->choices,
            ),
            $response->model,
            $prompt,
        );
    }

    public static function fromAnthropicResponse(
        AnthropicResponses\Messages\CreateResponse $response,
        string $prompt,
    ): self {
        $responses = [];

        foreach ($response->content as $index => $content) {
            $responses[] = Model\CompletionResponse::fromAnthropicContent($index, $content, $response->stop_reason);
        }

        return new self($responses, $response->model, $prompt);
    }

    /**
     * @phpstan-param SolutionArray $solution
     */
    public static function fromArray(array $solution): self
    {
        $responses = \array_map(
            Model\CompletionResponse::fromArray(...),
            $solution['responses'],
        );

        return new self($responses, $solution['model'], $solution['prompt']);
    }

    public function count(): int
    {
        return \count($this->responses);
    }

    public function getCreateDate(): ?\DateTimeInterface
    {
        return $this->createDate;
    }

    public function setCreateDate(\DateTimeInterface $createDate): self
    {
        $this->createDate = $createDate;

        return $this;
    }

    public function getCacheIdentifier(): ?string
    {
        return $this->cacheIdentifier;
    }

    public function setCacheIdentifier(string $cacheIdentifier): self
    {
        $this->cacheIdentifier = $cacheIdentifier;

        return $this;
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->responses);
    }

    /**
     * @phpstan-return SolutionArray
     */
    public function toArray(): array
    {
        return [
            'responses' => \array_map(
                static fn(ProblemSolving\Solution\Model\CompletionResponse $response): array => $response->toArray(),
                $this->responses,
            ),
            'model' => $this->model,
            'prompt' => $this->prompt,
        ];
    }

    /**
     * @phpstan-return SolutionArray
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
