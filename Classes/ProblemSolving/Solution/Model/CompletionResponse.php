<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS extension "solver".
 *
 * Copyright (C) 2023-2026 Elias Häußler <elias@haeussler.dev>
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

namespace EliasHaeussler\Typo3Solver\ProblemSolving\Solution\Model;

use OpenAI\Responses;

/**
 * CompletionResponse
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 *
 * @phpstan-import-type MessageArray from Message
 * @phpstan-type CompletionResponseArray array{index: int, message: MessageArray, finishReason: string|null}
 */
final readonly class CompletionResponse implements \JsonSerializable
{
    public function __construct(
        public int $index,
        public Message $message,
        public ?string $finishReason = null,
    ) {}

    public static function fromOpenAIChoice(
        Responses\Chat\CreateResponseChoice|Responses\Chat\CreateStreamedResponseChoice $choice,
    ): self {
        if ($choice instanceof Responses\Chat\CreateStreamedResponseChoice) {
            $role = (string)$choice->delta->role;
            $content = $choice->delta->content;
        } else {
            $role = $choice->message->role;
            $content = $choice->message->content;
        }

        return new self($choice->index, new Message($role, $content), $choice->finishReason);
    }

    /**
     * @param array{index?: int, message?: array{role?: string, content?: string|null}, finishReason?: string|null} $response
     */
    public static function fromArray(array $response): self
    {
        $message = $response['message'] ?? [];
        $message['role'] ??= '';
        $message['content'] ??= null;

        return new self(
            $response['index'] ?? 0,
            new Message($message['role'], $message['content']),
            $response['finishReason'] ?? null,
        );
    }

    public function merge(self $other): self
    {
        return new self(
            $this->index,
            new Message(
                $other->message->role,
                $this->message->content . $other->message->content,
            ),
            $other->finishReason,
        );
    }

    public function isFinished(): bool
    {
        return $this->finishReason !== null;
    }

    /**
     * @return CompletionResponseArray
     */
    public function toArray(): array
    {
        return [
            'index' => $this->index,
            'message' => $this->message->toArray(),
            'finishReason' => $this->finishReason,
        ];
    }

    /**
     * @return CompletionResponseArray
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
