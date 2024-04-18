<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS extension "solver".
 *
 * Copyright (C) 2024 Elias Häußler <elias@haeussler.dev>
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

namespace EliasHaeussler\Typo3Solver\Tests\Unit\Cache\Serializer;

use DateTimeImmutable;
use EliasHaeussler\Typo3Solver as Src;
use PHPUnit\Framework;
use TYPO3\TestingFramework;

use function time;

/**
 * SolutionSerializerTest
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
final class SolutionSerializerTest extends TestingFramework\Core\Unit\UnitTestCase
{
    private Src\Cache\Serializer\SolutionSerializer $subject;
    private Src\Configuration\Configuration $configuration;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new Src\Cache\Serializer\SolutionSerializer();
        $this->configuration = new Src\Configuration\Configuration();
    }

    #[Framework\Attributes\Test]
    public function serializeReturnsSerializedSolution(): void
    {
        $solution = Src\Tests\Unit\DataProvider\SolutionDataProvider::get();

        $createTime = time();
        $expiryTime = $createTime + $this->configuration->getCacheLifetime();

        $actual = $this->subject->serialize($solution);

        self::assertSame($solution->toArray(), $actual['solution']);
        self::assertEqualsWithDelta($createTime, $actual['createdAt'], 2.0);
        self::assertEqualsWithDelta($expiryTime, $actual['validUntil'], 2.0);
    }

    #[Framework\Attributes\Test]
    public function deserializeReturnsNullIfSolutionIsExpired(): void
    {
        $solutionArray = [
            'solution' => Src\Tests\Unit\DataProvider\SolutionDataProvider::get()->toArray(),
            'createdAt' => 0,
            'validUntil' => 0,
        ];

        self::assertNull($this->subject->deserialize($solutionArray));
    }

    #[Framework\Attributes\Test]
    public function deserializeReturnsDeserializedSolution(): void
    {
        $solution = Src\Tests\Unit\DataProvider\SolutionDataProvider::get();

        $createTime = time();
        $expiryTime = $createTime + $this->configuration->getCacheLifetime();

        $solutionArray = [
            'solution' => $solution->toArray(),
            'createdAt' => $createTime,
            'validUntil' => $expiryTime,
        ];

        $expected = clone $solution;
        $expected->setCreateDate(new DateTimeImmutable('@' . $createTime));

        self::assertEquals($expected, $this->subject->deserialize($solutionArray));
    }
}
