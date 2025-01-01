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

namespace EliasHaeussler\Typo3Solver\Tests\Unit\ProblemSolving\Solution;

use EliasHaeussler\Typo3Solver as Src;
use OpenAI\Responses;
use PHPUnit\Framework;
use TYPO3\TestingFramework;

/**
 * SolutionTest
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
final class SolutionTest extends TestingFramework\Core\Unit\UnitTestCase
{
    private Responses\Chat\CreateResponseChoice $choice;
    private Src\ProblemSolving\Solution\Solution $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->choice = Responses\Chat\CreateResponseChoice::from([
            'index' => 0,
            'message' => [
                'role' => '',
                'content' => 'hello world',
                'function_call' => null,
                'tool_calls' => null,
            ],
            'finish_reason' => null,
        ]);
        $this->subject = new Src\ProblemSolving\Solution\Solution([$this->choice], 'foo', 'baz');
    }

    #[Framework\Attributes\Test]
    public function fromResponseReturnsSolution(): void
    {
        $attributes = [
            'id' => 'id',
            'object' => 'object',
            'created' => 123,
            'model' => 'model',
            'choices' => [
                [
                    'index' => 0,
                    'message' => [
                        'role' => 'role',
                        'content' => 'content',
                        'function_call' => null,
                        'tool_calls' => null,
                    ],
                    'finish_reason' => null,
                ],
            ],
            'usage' => [
                'prompt_tokens' => 123,
                'completion_tokens' => 123,
                'total_tokens' => 123,
            ],
        ];

        if (\class_exists(Responses\Meta\MetaInformation::class)) {
            $meta = Responses\Meta\MetaInformation::from([
                'x-request-id' => ['foo'],
                'openai-model' => ['foo'],
                'openai-organization' => ['foo'],
                'openai-version' => ['foo'],
                'openai-processing-ms' => ['foo'],
                'x-ratelimit-limit-requests' => ['foo'],
                'x-ratelimit-remaining-requests' => ['foo'],
                'x-ratelimit-reset-requests' => ['foo'],
                'x-ratelimit-limit-tokens' => ['foo'],
                'x-ratelimit-remaining-tokens' => ['foo'],
                'x-ratelimit-reset-tokens' => ['foo'],
            ]);
            $response = Responses\Chat\CreateResponse::from($attributes, $meta);
        } else {
            $response = Responses\Chat\CreateResponse::from($attributes);
        }

        $choice = Responses\Chat\CreateResponseChoice::from([
            'index' => 0,
            'message' => [
                'role' => 'role',
                'content' => 'content',
                'function_call' => null,
                'tool_calls' => null,
            ],
            'finish_reason' => null,
        ]);

        $actual = Src\ProblemSolving\Solution\Solution::fromResponse($response, 'prompt');

        self::assertSame('prompt', $actual->getPrompt());
        self::assertSame('model', $actual->getModel());
        self::assertEquals([$choice], $actual->getChoices());
    }

    #[Framework\Attributes\Test]
    public function fromArrayReturnsSolution(): void
    {
        $solution = [
            'choices' => [
                [
                    'index' => 0,
                    'message' => [
                        'role' => 'role 1',
                        'content' => 'content 1',
                    ],
                    'finish_reason' => null,
                ],
                [
                    'index' => 1,
                    'message' => [
                        'role' => 'role 2',
                        'content' => 'content 2',
                    ],
                    'finish_reason' => null,
                ],
            ],
            'model' => 'model',
            'prompt' => 'prompt',
        ];

        $choice1 = Responses\Chat\CreateResponseChoice::from([
            'index' => 0,
            'message' => [
                'role' => 'role 1',
                'content' => 'content 1',
                'function_call' => null,
                'tool_calls' => null,
            ],
            'finish_reason' => null,
        ]);
        $choice2 = Responses\Chat\CreateResponseChoice::from([
            'index' => 1,
            'message' => [
                'role' => 'role 2',
                'content' => 'content 2',
                'function_call' => null,
                'tool_calls' => null,
            ],
            'finish_reason' => null,
        ]);

        $actual = Src\ProblemSolving\Solution\Solution::fromArray($solution);

        self::assertSame('prompt', $actual->getPrompt());
        self::assertSame('model', $actual->getModel());
        self::assertEquals([$choice1, $choice2], $actual->getChoices());
    }

    #[Framework\Attributes\Test]
    public function setCreateDateAppliesCreateDate(): void
    {
        self::assertNull($this->subject->getCreateDate());

        $createDate = new \DateTimeImmutable();

        self::assertSame($createDate, $this->subject->setCreateDate($createDate)->getCreateDate());
    }

    #[Framework\Attributes\Test]
    public function setCacheIdentifierAppliesCacheIdentifier(): void
    {
        self::assertNull($this->subject->getCacheIdentifier());
        self::assertSame('foo', $this->subject->setCacheIdentifier('foo')->getCacheIdentifier());
    }

    #[Framework\Attributes\Test]
    public function subjectIsCountable(): void
    {
        self::assertCount(1, $this->subject);
    }

    #[Framework\Attributes\Test]
    public function subjectIsIterable(): void
    {
        self::assertSame([$this->choice], \iterator_to_array($this->subject));
    }

    #[Framework\Attributes\Test]
    public function toArrayReturnsArrayRepresentation(): void
    {
        $expected = [
            'choices' => [
                $this->choice->toArray(),
            ],
            'model' => 'foo',
            'prompt' => 'baz',
        ];

        self::assertSame($expected, $this->subject->toArray());
    }

    #[Framework\Attributes\Test]
    public function subjectIsJsonSerializable(): void
    {
        $expected = [
            'choices' => [
                $this->choice->toArray(),
            ],
            'model' => 'foo',
            'prompt' => 'baz',
        ];

        self::assertJsonStringEqualsJsonString(
            \json_encode($expected, JSON_THROW_ON_ERROR),
            \json_encode($this->subject, JSON_THROW_ON_ERROR),
        );
    }
}
