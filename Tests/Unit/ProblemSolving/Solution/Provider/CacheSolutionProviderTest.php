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

namespace EliasHaeussler\Typo3Solver\Tests\Unit\ProblemSolving\Solution\Provider;

use EliasHaeussler\Typo3Solver as Src;
use EliasHaeussler\Typo3Solver\Tests;
use Exception;
use OpenAI\Responses;
use TYPO3\TestingFramework;

use function iterator_to_array;

/**
 * CacheSolutionProviderTest
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
final class CacheSolutionProviderTest extends TestingFramework\Core\Unit\UnitTestCase
{
    private Src\Cache\SolutionsCache $cache;
    private Tests\Unit\Fixtures\DummyStreamedSolutionProvider $provider;
    private Src\ProblemSolving\Solution\Provider\CacheSolutionProvider $subject;

    protected function setUp(): void
    {
        $this->cache = new Src\Cache\SolutionsCache();
        $this->provider = new Tests\Unit\Fixtures\DummyStreamedSolutionProvider();
        $this->subject = new Src\ProblemSolving\Solution\Provider\CacheSolutionProvider($this->cache, $this->provider);

        $this->cache->flush();
    }

    /**
     * @test
     */
    public function getSolutionReturnsSolutionFromCache(): void
    {
        $problem = new Src\ProblemSolving\Problem\Problem(new Exception(), $this->provider, 'foo');
        $solution = new Src\ProblemSolving\Solution\Solution([], 'foo', 'baz');

        $this->cache->set($problem, $solution);

        $actual = $this->subject->getSolution($problem);

        self::assertEquals($this->cache->get($problem), $actual);
    }

    /**
     * @test
     */
    public function getSolutionReturnsSolutionFromProviderAndStoresItInCache(): void
    {
        $problem = new Src\ProblemSolving\Problem\Problem(new Exception(), $this->provider, 'foo');
        $solution = new Src\ProblemSolving\Solution\Solution([], 'foo', 'baz');

        self::assertNull($this->cache->get($problem));

        $actual = $this->subject->getSolution($problem);

        self::assertEquals($solution, $actual);
        self::assertNotNull($this->cache->get($problem));
    }

    /**
     * @test
     */
    public function getStreamedSolutionYieldsSolutionFromCache(): void
    {
        $problem = new Src\ProblemSolving\Problem\Problem(new Exception(), $this->provider, 'foo');
        $solution = new Src\ProblemSolving\Solution\Solution([], 'foo', 'baz');

        $this->cache->set($problem, $solution);

        $actual = iterator_to_array($this->subject->getStreamedSolution($problem));

        self::assertCount(1, $actual);
        self::assertEquals([$this->cache->get($problem)], $actual);
    }

    /**
     * @test
     */
    public function getStreamedSolutionYieldsSolutionFromNonStreamedProvider(): void
    {
        $problem = new Src\ProblemSolving\Problem\Problem(new Exception(), $this->provider, 'foo');
        $solution = new Src\ProblemSolving\Solution\Solution([], 'foo', 'baz');

        $provider = new Tests\Unit\Fixtures\DummySolutionProvider();
        $provider->solution = $solution;
        $subject = new Src\ProblemSolving\Solution\Provider\CacheSolutionProvider($this->cache, $provider);

        $actual = iterator_to_array($subject->getStreamedSolution($problem));

        self::assertCount(1, $actual);
        self::assertSame([$solution], $actual);
    }

    /**
     * @test
     */
    public function getStreamedSolutionYieldsSolutionsFromProvider(): void
    {
        $problem = new Src\ProblemSolving\Problem\Problem(new Exception(), $this->provider, 'foo');

        $this->provider->solutionStream = [
            new Src\ProblemSolving\Solution\Solution(
                [
                    Responses\Chat\CreateStreamedResponseChoice::from([
                        'index' => 0,
                        'delta' => [
                            'role' => 'role 1',
                            'content' => 'content 1',
                        ],
                        'finish_reason' => null,
                    ]),
                    Responses\Chat\CreateStreamedResponseChoice::from([
                        'index' => 1,
                        'delta' => [
                            'role' => 'role 2',
                            'content' => 'content 2',
                        ],
                        'finish_reason' => null,
                    ]),
                ],
                'foo',
                'baz',
            ),
            new Src\ProblemSolving\Solution\Solution(
                [
                    Responses\Chat\CreateStreamedResponseChoice::from([
                        'index' => 0,
                        'delta' => [
                            'role' => 'role 1',
                            'content' => ' ... end 1',
                        ],
                        'finish_reason' => null,
                    ]),
                    Responses\Chat\CreateStreamedResponseChoice::from([
                        'index' => 1,
                        'delta' => [
                            'role' => 'role 2',
                            'content' => ' ... end 2',
                        ],
                        'finish_reason' => null,
                    ]),
                ],
                'foo',
                'baz',
            ),
        ];

        $expected1 = new Src\ProblemSolving\Solution\Solution(
            [
                Responses\Chat\CreateResponseChoice::from([
                    'index' => 0,
                    'message' => [
                        'role' => 'role 1',
                        'content' => 'content 1',
                    ],
                    'finish_reason' => null,
                ]),
                Responses\Chat\CreateResponseChoice::from([
                    'index' => 1,
                    'message' => [
                        'role' => 'role 2',
                        'content' => 'content 2',
                    ],
                    'finish_reason' => null,
                ]),
            ],
            'foo',
            'baz',
        );
        $expected2 = new Src\ProblemSolving\Solution\Solution(
            [
                Responses\Chat\CreateResponseChoice::from([
                    'index' => 0,
                    'message' => [
                        'role' => 'role 1',
                        'content' => 'content 1 ... end 1',
                    ],
                    'finish_reason' => null,
                ]),
                Responses\Chat\CreateResponseChoice::from([
                    'index' => 1,
                    'message' => [
                        'role' => 'role 2',
                        'content' => 'content 2 ... end 2',
                    ],
                    'finish_reason' => null,
                ]),
            ],
            'foo',
            'baz',
        );

        $actual = iterator_to_array($this->subject->getStreamedSolution($problem));

        self::assertCount(2, $actual);
        self::assertEquals($expected1, $actual[0]);
        self::assertEquals($expected2, $actual[1]);
    }

    /**
     * @test
     */
    public function canBeUsedDelegatesRequestToProvider(): void
    {
        $exception = new Exception();

        $this->provider->shouldBeUsed = true;

        self::assertTrue($this->subject->canBeUsed($exception));

        $this->provider->shouldBeUsed = false;

        self::assertFalse($this->subject->canBeUsed($exception));
    }

    /**
     * @test
     */
    public function isCacheableReturnsTrue(): void
    {
        self::assertTrue($this->subject->isCacheable());
    }

    /**
     * @test
     */
    public function getProviderReturnsProvider(): void
    {
        self::assertSame($this->provider, $this->subject->getProvider());
    }
}
