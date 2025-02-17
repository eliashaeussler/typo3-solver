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

namespace EliasHaeussler\Typo3Solver\Tests\Unit\ProblemSolving\Solution\Provider;

use EliasHaeussler\Typo3Solver as Src;
use EliasHaeussler\Typo3Solver\Tests;
use PHPUnit\Framework;
use TYPO3\TestingFramework;

/**
 * CacheSolutionProviderTest
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
#[Framework\Attributes\CoversClass(Src\ProblemSolving\Solution\Provider\CacheSolutionProvider::class)]
final class CacheSolutionProviderTest extends TestingFramework\Core\Unit\UnitTestCase
{
    private Src\Cache\SolutionsCache $cache;
    private Tests\Unit\Fixtures\DummyStreamedSolutionProvider $provider;
    private Src\ProblemSolving\Solution\Provider\CacheSolutionProvider $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cache = new Src\Cache\SolutionsCache();
        $this->provider = new Tests\Unit\Fixtures\DummyStreamedSolutionProvider();
        $this->subject = new Src\ProblemSolving\Solution\Provider\CacheSolutionProvider($this->cache, $this->provider);

        $this->cache->flush();
    }

    #[Framework\Attributes\Test]
    public function createThrowsExceptionIfDelegatingProviderIsNotGiven(): void
    {
        $this->expectExceptionObject(
            Src\Exception\MissingSolutionProviderException::forDelegate(),
        );

        Src\ProblemSolving\Solution\Provider\CacheSolutionProvider::create();
    }

    #[Framework\Attributes\Test]
    public function createReturnsInitializedProviderWithGivenDelegate(): void
    {
        self::assertEquals(
            $this->subject,
            Src\ProblemSolving\Solution\Provider\CacheSolutionProvider::create($this->provider),
        );
    }

    #[Framework\Attributes\Test]
    public function getSolutionReturnsSolutionFromCache(): void
    {
        $problem = Tests\Unit\DataProvider\ProblemDataProvider::get(solutionProvider: $this->provider);
        $solution = Tests\Unit\DataProvider\SolutionDataProvider::get();

        $this->cache->set($problem, $solution);

        $actual = $this->subject->getSolution($problem);

        self::assertEquals($this->cache->get($problem), $actual);
    }

    #[Framework\Attributes\Test]
    public function getSolutionReturnsSolutionFromProviderAndStoresItInCache(): void
    {
        $problem = Tests\Unit\DataProvider\ProblemDataProvider::get(solutionProvider: $this->provider);
        $solution = Tests\Unit\DataProvider\SolutionDataProvider::get();

        $this->provider->solution = $solution;

        self::assertNull($this->cache->get($problem));

        $actual = $this->subject->getSolution($problem);

        self::assertEquals($solution, $actual);
        self::assertNotNull($this->cache->get($problem));
    }

    #[Framework\Attributes\Test]
    public function getStreamedSolutionYieldsSolutionFromCache(): void
    {
        $problem = Tests\Unit\DataProvider\ProblemDataProvider::get(solutionProvider: $this->provider);
        $solution = Tests\Unit\DataProvider\SolutionDataProvider::get();

        $this->cache->set($problem, $solution);

        $actual = \iterator_to_array($this->subject->getStreamedSolution($problem));

        self::assertCount(1, $actual);
        self::assertEquals([$this->cache->get($problem)], $actual);
    }

    #[Framework\Attributes\Test]
    public function getStreamedSolutionYieldsSolutionFromNonStreamedProvider(): void
    {
        $problem = Tests\Unit\DataProvider\ProblemDataProvider::get(solutionProvider: $this->provider);
        $solution = Tests\Unit\DataProvider\SolutionDataProvider::get();

        $provider = new Tests\Unit\Fixtures\DummySolutionProvider();
        $provider->solution = $solution;
        $subject = new Src\ProblemSolving\Solution\Provider\CacheSolutionProvider($this->cache, $provider);

        $actual = \iterator_to_array($subject->getStreamedSolution($problem));

        self::assertCount(1, $actual);
        self::assertSame([$solution], $actual);
    }

    #[Framework\Attributes\Test]
    public function getStreamedSolutionYieldsSolutionsFromProvider(): void
    {
        $problem = Tests\Unit\DataProvider\ProblemDataProvider::get(solutionProvider: $this->provider);
        $solution = \iterator_to_array(Tests\Unit\DataProvider\SolutionDataProvider::getStream(2, 2));

        $this->provider->solutionStream = $solution;

        $expected1 = Tests\Unit\DataProvider\SolutionDataProvider::get(2);
        $expected2 = Tests\Unit\DataProvider\SolutionDataProvider::get(2, ' ... message {index}');

        $actual = \iterator_to_array($this->subject->getStreamedSolution($problem));

        self::assertCount(2, $actual);
        self::assertEquals($expected1, $actual[0]);
        self::assertEquals($expected2, $actual[1]);
    }

    #[Framework\Attributes\Test]
    public function canBeUsedDelegatesRequestToProvider(): void
    {
        $exception = new \Exception();

        $this->provider->shouldBeUsed = true;

        self::assertTrue($this->subject->canBeUsed($exception));

        $this->provider->shouldBeUsed = false;

        self::assertFalse($this->subject->canBeUsed($exception));
    }

    #[Framework\Attributes\Test]
    public function isCacheableReturnsTrue(): void
    {
        self::assertTrue($this->subject->isCacheable());
    }

    #[Framework\Attributes\Test]
    public function listModelsReturnsModelsFromDelegatedProvider(): void
    {
        $this->provider->models = [
            new Src\ProblemSolving\Solution\Provider\Model\AiModel(
                'gpt-3.5',
                new \DateTimeImmutable('now'),
            ),
            new Src\ProblemSolving\Solution\Provider\Model\AiModel(
                'gpt-3.5-turbo-0301',
                new \DateTimeImmutable('yesterday'),
            ),
        ];

        self::assertSame($this->provider->models, $this->subject->listModels());
    }

    #[Framework\Attributes\Test]
    public function getProviderReturnsProvider(): void
    {
        self::assertSame($this->provider, $this->subject->getProvider());
    }
}
