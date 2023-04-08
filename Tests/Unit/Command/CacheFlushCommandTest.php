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

namespace EliasHaeussler\Typo3Solver\Tests\Unit\Command;

use EliasHaeussler\Typo3Solver as Src;
use Symfony\Component\Console;
use TYPO3\TestingFramework;

use function sprintf;

/**
 * CacheFlushCommandTest
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
final class CacheFlushCommandTest extends TestingFramework\Core\Unit\UnitTestCase
{
    private Src\Cache\SolutionsCache $cache;
    private Console\Tester\CommandTester $commandTester;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cache = new Src\Cache\SolutionsCache();
        $this->commandTester = new Console\Tester\CommandTester(new Src\Command\CacheFlushCommand($this->cache));

        $this->cache->flush();
    }

    /**
     * @test
     */
    public function executeRemovesSpecificCacheEntryFromCache(): void
    {
        $problem = Src\Tests\Unit\DataProvider\ProblemDataProvider::get();
        $solution = Src\Tests\Unit\DataProvider\SolutionDataProvider::get();

        $this->cache->set($problem, $solution);

        $identifier = $this->cache->get($problem)?->getCacheIdentifier();

        self::assertNotNull($identifier);
        self::assertNotNull($this->cache->get($problem));

        $this->commandTester->execute(['identifier' => $identifier]);

        self::assertSame(0, $this->commandTester->getStatusCode());
        self::assertStringContainsString(
            sprintf('Cache entry with identifier "%s" successfully removed.', $identifier),
            $this->commandTester->getDisplay(),
        );
        self::assertNull($this->cache->get($problem));
    }

    /**
     * @test
     */
    public function executeFlushesCompleteCache(): void
    {
        $problem1 = Src\Tests\Unit\DataProvider\ProblemDataProvider::get('message 1');
        $problem2 = Src\Tests\Unit\DataProvider\ProblemDataProvider::get('message 2');
        $solution1 = Src\Tests\Unit\DataProvider\SolutionDataProvider::get();
        $solution2 = Src\Tests\Unit\DataProvider\SolutionDataProvider::get();

        $this->cache->set($problem1, $solution1);
        $this->cache->set($problem2, $solution2);

        self::assertNotNull($this->cache->get($problem1));
        self::assertNotNull($this->cache->get($problem2));

        $this->commandTester->execute([]);

        self::assertSame(0, $this->commandTester->getStatusCode());
        self::assertStringContainsString('Solutions cache successfully cleared.', $this->commandTester->getDisplay());
        self::assertNull($this->cache->get($problem1));
        self::assertNull($this->cache->get($problem2));
    }
}
