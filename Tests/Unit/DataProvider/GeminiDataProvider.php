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

use GeminiAPI\Enums;
use GeminiAPI\Resources;
use PHPUnit\Framework;

/**
 * GeminiDataProvider
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
final class GeminiDataProvider
{
    public static function fillCandidateProperties(
        Resources\Candidate&Framework\MockObject\MockObject $mock,
        int $index = 0,
        Enums\FinishReason $finishReason = Enums\FinishReason::MAX_TOKENS,
        ?Resources\Parts\PartInterface $part = null,
    ): void {
        $reflection = new \ReflectionClass(Resources\Candidate::class);
        $reflection->getProperty('index')->setValue($mock, $index);
        $reflection->getProperty('finishReason')->setValue($mock, $finishReason);
        $reflection->getProperty('content')->setValue(
            $mock,
            new Resources\Content(
                [
                    $part ?? new Resources\Parts\TextPart('foo'),
                ],
                Enums\Role::Model,
            ),
        );
    }
}
