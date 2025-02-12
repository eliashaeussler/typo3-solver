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

namespace EliasHaeussler\Typo3Solver\Tests\Unit\ProblemSolving\Solution\Provider\Model;

use EliasHaeussler\Typo3Solver as Src;
use OpenAI\Responses;
use PHPUnit\Framework;
use TYPO3\TestingFramework;

/**
 * AiModelTest
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
#[Framework\Attributes\CoversClass(Src\ProblemSolving\Solution\Provider\Model\AiModel::class)]
final class AiModelTest extends TestingFramework\Core\Unit\UnitTestCase
{
    #[Framework\Attributes\Test]
    public function fromOpenAIRetrieveResponseReturnsAiModelFromGivenResponse(): void
    {
        $response = Responses\Models\RetrieveResponse::fake([
            'id' => 'foo',
            'created' => 1739347287,
        ]);

        $actual = Src\ProblemSolving\Solution\Provider\Model\AiModel::fromOpenAIRetrieveResponse($response);

        self::assertSame('foo', $actual->name);
        self::assertSame(1739347287, $actual->createdAt?->getTimestamp());
    }
}
