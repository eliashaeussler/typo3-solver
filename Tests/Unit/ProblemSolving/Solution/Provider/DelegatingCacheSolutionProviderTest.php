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

/**
 * DelegatingCacheSolutionProviderTest
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
final class DelegatingCacheSolutionProviderTest extends TestingFramework\Core\Unit\UnitTestCase
{
    private Src\Cache\SolutionsCache $cache;
    private Tests\Unit\Fixtures\DummySolutionProvider $provider;
    private Src\ProblemSolving\Solution\Provider\DelegatingCacheSolutionProvider $subject;

    protected function setUp(): void
    {
        $this->cache = new Src\Cache\SolutionsCache();
        $this->provider = new Tests\Unit\Fixtures\DummySolutionProvider();
        $this->subject = new Src\ProblemSolving\Solution\Provider\DelegatingCacheSolutionProvider($this->cache, $this->provider);

        $this->cache->flush();
    }

    /**
     * @test
     */
    public function getSolutionReturnsSolutionFromCache(): void
    {
        $problem = new Src\ProblemSolving\Problem\Problem(new Exception(), $this->provider, 'foo');
        $solution = new Src\ProblemSolving\Solution\Solution(
            [
                Responses\Chat\CreateResponseChoice::from([
                    'index' => 0,
                    'message' => [
                        'role' => '',
                        'content' => 'Please wait…',
                    ],
                    'finish_reason' => null,
                ]),
            ],
            'Please wait…',
            'Please wait…',
        );

        $actual = $this->subject->getSolution($problem);

        self::assertEquals($solution, $actual);
    }

    /**
     * @test
     */
    public function getSolutionReturnsDummySolution(): void
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
    public function canBeUsedReturnsTrue(): void
    {
        self::assertTrue($this->subject->canBeUsed(new Exception()));
    }

    /**
     * @test
     */
    public function isCacheableReturnsFalse(): void
    {
        self::assertFalse($this->subject->isCacheable());
    }
}
