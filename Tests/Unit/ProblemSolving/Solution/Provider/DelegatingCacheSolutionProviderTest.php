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
 * DelegatingCacheSolutionProviderTest
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
#[Framework\Attributes\CoversClass(Src\ProblemSolving\Solution\Provider\DelegatingCacheSolutionProvider::class)]
final class DelegatingCacheSolutionProviderTest extends TestingFramework\Core\Unit\UnitTestCase
{
    private Src\Cache\SolutionsCache $cache;
    private Tests\Unit\Fixtures\DummySolutionProvider $delegate;
    private Src\ProblemSolving\Solution\Provider\DelegatingCacheSolutionProvider $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cache = new Src\Cache\SolutionsCache();
        $this->delegate = new Tests\Unit\Fixtures\DummySolutionProvider();
        $this->subject = new Src\ProblemSolving\Solution\Provider\DelegatingCacheSolutionProvider($this->cache, $this->delegate);

        $this->cache->flush();
    }

    #[Framework\Attributes\Test]
    public function createThrowsExceptionIfDelegatingProviderIsNotGiven(): void
    {
        $this->expectExceptionObject(
            Src\Exception\MissingSolutionProviderException::forDelegate(),
        );

        Src\ProblemSolving\Solution\Provider\DelegatingCacheSolutionProvider::create();
    }

    #[Framework\Attributes\Test]
    public function createReturnsInitializedProviderWithGivenDelegate(): void
    {
        self::assertEquals(
            $this->subject,
            Src\ProblemSolving\Solution\Provider\DelegatingCacheSolutionProvider::create($this->delegate),
        );
    }

    #[Framework\Attributes\Test]
    public function getSolutionReturnsSolutionFromCache(): void
    {
        $problem = Tests\Unit\DataProvider\ProblemDataProvider::get(solutionProvider: $this->delegate);
        $solution = new Src\ProblemSolving\Solution\Solution(
            [
                new Src\ProblemSolving\Solution\Model\CompletionResponse(
                    0,
                    new Src\ProblemSolving\Solution\Model\Message('', 'Please wait…'),
                ),
            ],
            'Please wait…',
            'Please wait…',
        );

        $actual = $this->subject->getSolution($problem);

        self::assertEquals($solution, $actual);
    }

    #[Framework\Attributes\Test]
    public function getSolutionReturnsDummySolution(): void
    {
        $problem = Tests\Unit\DataProvider\ProblemDataProvider::get(solutionProvider: $this->delegate);
        $solution = Tests\Unit\DataProvider\SolutionDataProvider::get();

        $this->cache->set($problem, $solution);

        $actual = $this->subject->getSolution($problem);

        self::assertEquals($this->cache->get($problem), $actual);
    }

    #[Framework\Attributes\Test]
    public function canBeUsedReturnsTrue(): void
    {
        self::assertTrue($this->subject->canBeUsed(new \Exception()));
    }

    #[Framework\Attributes\Test]
    public function isCacheableReturnsFalse(): void
    {
        self::assertFalse($this->subject->isCacheable());
    }

    #[Framework\Attributes\Test]
    public function listModelsReturnsModelsFromDelegate(): void
    {
        $this->delegate->models = [
            new Src\ProblemSolving\Solution\Provider\Model\AiModel(
                'gpt-3.5',
                new \DateTimeImmutable('now'),
            ),
            new Src\ProblemSolving\Solution\Provider\Model\AiModel(
                'gpt-3.5-turbo-0301',
                new \DateTimeImmutable('yesterday'),
            ),
        ];

        self::assertSame($this->delegate->models, $this->subject->listModels());
    }
}
