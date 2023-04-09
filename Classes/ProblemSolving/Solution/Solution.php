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

namespace EliasHaeussler\Typo3Solver\ProblemSolving\Solution;

use ArrayIterator;
use Countable;
use DateTimeInterface;
use IteratorAggregate;
use JsonSerializable;
use OpenAI\Responses;
use Traversable;

use function array_map;
use function array_values;
use function count;

/**
 * Solution.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 *
 * @implements IteratorAggregate<int, Responses\Chat\CreateResponseChoice>
 *
 * @phpstan-type SolutionArray array{choices: list<array<string, mixed>>, model: string, prompt: string}
 */
final class Solution implements Countable, IteratorAggregate, JsonSerializable
{
    private ?DateTimeInterface $createDate = null;
    private ?string $cacheIdentifier = null;

    /**
     * @param list<Responses\Chat\CreateResponseChoice> $choices
     */
    public function __construct(
        private readonly array $choices,
        private readonly string $model,
        private readonly string $prompt,
    ) {
    }

    public static function fromResponse(Responses\Chat\CreateResponse $response, string $prompt): self
    {
        return new self(array_values($response->choices), $response->model, $prompt);
    }

    /**
     * @phpstan-param SolutionArray $solution
     */
    public static function fromArray(array $solution): self
    {
        $choices = array_map(
            /* @phpstan-ignore-next-line */
            static fn (array $choice): Responses\Chat\CreateResponseChoice => Responses\Chat\CreateResponseChoice::from($choice),
            $solution['choices'],
        );

        return new self($choices, $solution['model'], $solution['prompt']);
    }

    public function count(): int
    {
        return count($this->choices);
    }

    /**
     * @return list<Responses\Chat\CreateResponseChoice>
     */
    public function getChoices(): array
    {
        return $this->choices;
    }

    public function getModel(): string
    {
        return $this->model;
    }

    public function getPrompt(): string
    {
        return $this->prompt;
    }

    public function getCreateDate(): ?DateTimeInterface
    {
        return $this->createDate;
    }

    public function setCreateDate(DateTimeInterface $createDate): self
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

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->choices);
    }

    /**
     * @phpstan-return SolutionArray
     */
    public function toArray(): array
    {
        return [
            'choices' => array_map(
                static fn (Responses\Chat\CreateResponseChoice $choice): array => $choice->toArray(),
                $this->choices,
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
