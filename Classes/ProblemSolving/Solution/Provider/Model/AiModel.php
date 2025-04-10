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

namespace EliasHaeussler\Typo3Solver\ProblemSolving\Solution\Provider\Model;

use OpenAI\Responses;

/**
 * AiModel
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
final class AiModel
{
    public function __construct(
        public readonly string $name,
        public readonly ?\DateTimeImmutable $createdAt = null,
    ) {}

    public static function fromOpenAIRetrieveResponse(Responses\Models\RetrieveResponse $response): self
    {
        return new self($response->id, new \DateTimeImmutable('@' . $response->created));
    }

    /**
     * @param array{created_at: string, display_name: string, id: string, type: string} $model
     */
    public static function fromAnthropicModel(array $model): self
    {
        $createdAt = \DateTimeImmutable::createFromFormat(\DateTimeInterface::RFC3339, $model['created_at']);

        if ($createdAt === false) {
            $createdAt = null;
        }

        return new self($model['id'], $createdAt);
    }
}
