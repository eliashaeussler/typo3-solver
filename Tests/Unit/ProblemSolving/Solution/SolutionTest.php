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

namespace EliasHaeussler\Typo3Solver\Tests\Unit\ProblemSolving\Solution;

use DateTimeImmutable;
use EliasHaeussler\Typo3Solver as Src;
use GuzzleHttp\Psr7;
use OpenAI\Responses;
use TYPO3\TestingFramework;

use function iterator_to_array;
use function json_encode;

/**
 * SolutionTest
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
final class SolutionTest extends TestingFramework\Core\Unit\UnitTestCase
{
    private Responses\Chat\CreateResponseChoice $choice;
    private Src\ProblemSolving\Solution\Solution $subject;

    protected function setUp(): void
    {
        $this->choice = Responses\Chat\CreateResponseChoice::from([
            'index' => 0,
            'message' => [
                'role' => '',
                'content' => 'hello world',
            ],
            'finish_reason' => null,
        ]);
        $this->subject = new Src\ProblemSolving\Solution\Solution([$this->choice], 'foo', 'baz');
    }

    /**
     * @test
     */
    public function fromResponseReturnsSolution(): void
    {
        $response = Responses\Chat\CreateResponse::from([
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
                    ],
                    'finish_reason' => null,
                ],
            ],
            'usage' => [
                'prompt_tokens' => 123,
                'completion_tokens' => 123,
                'total_tokens' => 123,
            ],
        ]);
        $choice = Responses\Chat\CreateResponseChoice::from([
            'index' => 0,
            'message' => [
                'role' => 'role',
                'content' => 'content',
            ],
            'finish_reason' => null,
        ]);

        $actual = Src\ProblemSolving\Solution\Solution::fromResponse($response, 'prompt');

        self::assertSame('prompt', $actual->getPrompt());
        self::assertSame('model', $actual->getModel());
        self::assertEquals([$choice], $actual->getChoices());
    }

    /**
     * @test
     */
    public function fromStreamReturnsSolution(): void
    {
        $streamedResponse1 = [
            'id' => 'id',
            'object' => 'object',
            'created' => 123,
            'model' => 'model',
            'choices' => [
                [
                    'index' => 0,
                    'delta' => [
                        'role' => 'role',
                        'content' => 'content',
                    ],
                    'finish_reason' => null,
                ],
            ],
        ];
        $streamedResponse2 = [
            'id' => 'id 2',
            'object' => 'object 2',
            'created' => 1234,
            'model' => 'model 2',
            'choices' => [
                [
                    'index' => 0,
                    'delta' => [
                        'role' => 'role 2',
                        'content' => 'content 2',
                    ],
                    'finish_reason' => null,
                ],
            ],
        ];

        $response = new Psr7\Response();
        $response->getBody()->write('data: ' . json_encode($streamedResponse1) . PHP_EOL);
        $response->getBody()->write('data: ' . json_encode($streamedResponse2) . PHP_EOL);
        $response->getBody()->rewind();

        $stream = new Responses\StreamResponse(Responses\Chat\CreateStreamedResponse::class, $response);

        $choice1 = Responses\Chat\CreateResponseChoice::from([
            'index' => 0,
            'message' => [
                'role' => 'role',
                'content' => 'content',
            ],
            'finish_reason' => null,
        ]);
        $choice2 = Responses\Chat\CreateResponseChoice::from([
            'index' => 0,
            'message' => [
                'role' => 'role 2',
                'content' => 'content 2',
            ],
            'finish_reason' => null,
        ]);

        $actual = iterator_to_array(Src\ProblemSolving\Solution\Solution::fromStream($stream, 'prompt'));

        self::assertCount(2, $actual);
        self::assertSame('prompt', $actual[0]->getPrompt());
        self::assertSame('model', $actual[0]->getModel());
        self::assertEquals([$choice1], $actual[0]->getChoices());
        self::assertSame('prompt', $actual[1]->getPrompt());
        self::assertSame('model 2', $actual[1]->getModel());
        self::assertEquals([$choice2], $actual[1]->getChoices());
    }

    /**
     * @test
     */
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
            ],
            'finish_reason' => null,
        ]);
        $choice2 = Responses\Chat\CreateResponseChoice::from([
            'index' => 1,
            'message' => [
                'role' => 'role 2',
                'content' => 'content 2',
            ],
            'finish_reason' => null,
        ]);

        $actual = Src\ProblemSolving\Solution\Solution::fromArray($solution);

        self::assertSame('prompt', $actual->getPrompt());
        self::assertSame('model', $actual->getModel());
        self::assertEquals([$choice1, $choice2], $actual->getChoices());
    }

    /**
     * @test
     */
    public function setCreateDateAppliesCreateDate(): void
    {
        self::assertNull($this->subject->getCreateDate());

        $createDate = new DateTimeImmutable();

        self::assertSame($createDate, $this->subject->setCreateDate($createDate)->getCreateDate());
    }

    /**
     * @test
     */
    public function setCacheIdentifierAppliesCacheIdentifier(): void
    {
        self::assertNull($this->subject->getCacheIdentifier());
        self::assertSame('foo', $this->subject->setCacheIdentifier('foo')->getCacheIdentifier());
    }

    /**
     * @test
     */
    public function subjectIsCountable(): void
    {
        self::assertCount(1, $this->subject);
    }

    /**
     * @test
     */
    public function subjectIsIterable(): void
    {
        self::assertSame([$this->choice], iterator_to_array($this->subject));
    }

    /**
     * @test
     */
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

    /**
     * @test
     */
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
            json_encode($expected, JSON_THROW_ON_ERROR),
            json_encode($this->subject, JSON_THROW_ON_ERROR),
        );
    }
}
