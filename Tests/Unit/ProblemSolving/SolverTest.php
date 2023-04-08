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

namespace EliasHaeussler\Typo3Solver\Tests\Unit\ProblemSolving;

use EliasHaeussler\Typo3Solver as Src;
use EliasHaeussler\Typo3Solver\Tests;
use Exception;
use TYPO3\TestingFramework;

use function iterator_to_array;
use function json_encode;

/**
 * SolverTest
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
final class SolverTest extends TestingFramework\Core\Unit\UnitTestCase
{
    private Tests\Unit\Fixtures\DummyStreamedSolutionProvider $provider;
    private Src\Formatter\JsonFormatter $formatter;
    private Src\ProblemSolving\Solver $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->provider = new Tests\Unit\Fixtures\DummyStreamedSolutionProvider();
        $this->formatter = new Src\Formatter\JsonFormatter();
        $this->subject = new Src\ProblemSolving\Solver($this->provider, $this->formatter);

        (new Src\Cache\SolutionsCache())->flush();
    }

    /**
     * @test
     */
    public function solveReturnsNullIfProviderCannotBeUsed(): void
    {
        $this->provider->shouldBeUsed = false;

        self::assertNull($this->subject->solve(new Exception()));
    }

    /**
     * @test
     */
    public function solveReturnsFormattedSolution(): void
    {
        $dummySolution = Tests\Unit\DataProvider\SolutionDataProvider::get();

        $this->provider->solution = $dummySolution;

        $actual = $this->subject->solve(new Exception());

        self::assertNotNull($actual);
        self::assertJsonStringEqualsJsonString(json_encode($dummySolution, JSON_THROW_ON_ERROR), $actual);
    }

    /**
     * @test
     */
    public function solveStreamedYieldsEmptySolutionStreamIfProviderCannotBeUsed(): void
    {
        $this->provider->shouldBeUsed = false;

        self::assertSame([], iterator_to_array($this->subject->solveStreamed(new Exception())));
    }

    /**
     * @test
     */
    public function solveStreamedYieldsFormattedSolutionStreams(): void
    {
        $solutions = iterator_to_array(Tests\Unit\DataProvider\SolutionDataProvider::getStream());

        $this->provider->solutionStream = $solutions;

        $actual = iterator_to_array($this->subject->solveStreamed(new Exception()));

        $expected1 = Tests\Unit\DataProvider\SolutionDataProvider::get();
        $expected2 = Tests\Unit\DataProvider\SolutionDataProvider::get(message: 'message {index} ... message {index}');

        self::assertCount(2, $actual);
        self::assertJsonStringEqualsJsonString(json_encode($expected1, JSON_THROW_ON_ERROR), $actual[0]);
        self::assertJsonStringEqualsJsonString(json_encode($expected2, JSON_THROW_ON_ERROR), $actual[1]);
    }
}
