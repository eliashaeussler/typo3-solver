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

namespace EliasHaeussler\Typo3Solver\Tests\Unit\Cache;

use EliasHaeussler\Typo3Solver as Src;
use PHPUnit\Framework;
use Symfony\Component\Filesystem;
use TYPO3\TestingFramework;

/**
 * SolutionsCacheTest
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
#[Framework\Attributes\CoversClass(Src\Cache\SolutionsCache::class)]
final class SolutionsCacheTest extends TestingFramework\Core\Unit\UnitTestCase
{
    private Src\Cache\SolutionsCache $subject;
    private Src\ProblemSolving\Problem\Problem $problem;
    private Src\ProblemSolving\Solution\Solution $solution;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new Src\Cache\SolutionsCache();
        $this->problem = Src\Tests\Unit\DataProvider\ProblemDataProvider::get();
        $this->solution = Src\Tests\Unit\DataProvider\SolutionDataProvider::get();
    }

    #[Framework\Attributes\Test]
    public function constructorCreatesCacheFileIfNotExists(): void
    {
        $cacheFile = \dirname(__DIR__, 3) . '/var/cache/data/tx_solver/solutions.php';
        $filesystem = new Filesystem\Filesystem();

        $filesystem->remove($cacheFile);

        self::assertFileDoesNotExist($cacheFile);

        new Src\Cache\SolutionsCache();

        self::assertFileExists($cacheFile);
        self::assertSame([], require $cacheFile);
    }

    #[Framework\Attributes\Test]
    public function getReturnsNullIfSolutionProviderIsNotCacheable(): void
    {
        $solutionProvider = $this->problem->getSolutionProvider();

        self::assertInstanceOf(Src\Tests\Unit\Fixtures\DummySolutionProvider::class, $solutionProvider);

        $solutionProvider->isCacheable = false;

        self::assertNull($this->subject->get($this->problem));
    }

    #[Framework\Attributes\Test]
    public function getReturnsNullIfCacheIsDisabled(): void
    {
        // Disable cache
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][Src\Extension::KEY]['cache']['lifetime'] = 0;

        self::assertNull($this->subject->get($this->problem));
    }

    #[Framework\Attributes\Test]
    public function getReturnsNullOnEmptyCache(): void
    {
        $this->subject->flush();

        self::assertNull($this->subject->get($this->problem));
    }

    #[Framework\Attributes\Test]
    public function getReturnsCacheEntry(): void
    {
        $this->subject->flush();
        $this->subject->set($this->problem, $this->solution);

        $actual = $this->subject->get($this->problem);

        self::assertNotNull($actual);
        self::assertNotNull($actual->getCacheIdentifier());
        self::assertNotNull($actual->getCreateDate());

        $expected = clone $this->solution;
        $expected->setCacheIdentifier($actual->getCacheIdentifier());
        $expected->setCreateDate($actual->getCreateDate());

        self::assertEquals($expected, $actual);
    }

    #[Framework\Attributes\Test]
    public function getRemovesExpiredCacheEntryAndReturnsNull(): void
    {
        // Manipulate cache lifetime
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][Src\Extension::KEY]['cache']['lifetime'] = 2;

        $this->subject->flush();
        $this->subject->set($this->problem, $this->solution);

        self::assertNotNull($this->subject->get($this->problem));

        // Wait three seconds to assure cache is expired
        sleep(3);

        self::assertNull($this->subject->get($this->problem));
    }

    #[Framework\Attributes\Test]
    public function setDoesNothingIfSolutionProviderIsNotCacheable(): void
    {
        $this->subject->flush();

        $solutionProvider = $this->problem->getSolutionProvider();

        self::assertInstanceOf(Src\Tests\Unit\Fixtures\DummySolutionProvider::class, $solutionProvider);

        $solutionProvider->isCacheable = false;

        $this->subject->set($this->problem, $this->solution);

        self::assertNull($this->subject->get($this->problem));
    }

    #[Framework\Attributes\Test]
    public function setDoesNothingIfCacheIsDisabled(): void
    {
        // Disable cache
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][Src\Extension::KEY]['cache']['lifetime'] = 0;

        $this->subject->flush();
        $this->subject->set($this->problem, $this->solution);

        self::assertNull($this->subject->get($this->problem));
    }

    #[Framework\Attributes\Test]
    public function removeRemovesGivenExceptionFromCache(): void
    {
        $this->subject->set($this->problem, $this->solution);

        self::assertNotNull($this->subject->get($this->problem));

        $this->subject->remove($this->problem);

        self::assertNull($this->subject->get($this->problem));
    }
}
