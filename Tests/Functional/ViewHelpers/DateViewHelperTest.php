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

namespace EliasHaeussler\Typo3Solver\Tests\Functional\ViewHelpers;

use DateTimeImmutable;
use DateTimeInterface;
use EliasHaeussler\Typo3Solver\Tests;
use Generator;
use TYPO3\TestingFramework;

use function trim;

/**
 * DateViewHelperTest
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
final class DateViewHelperTest extends TestingFramework\Core\Functional\FunctionalTestCase
{
    use Tests\ViewTrait;

    protected array $testExtensionsToLoad = [
        'typo3conf/ext/solver',
    ];

    protected bool $initializeDatabase = false;

    /**
     * @test
     */
    public function renderStaticReturnsNonReadableFormattedDate(): void
    {
        $date = new DateTimeImmutable();

        $view = $this->createView('<s:date date="{date}" />');
        $view->assign('date', $date);

        self::assertSame(
            $date->format('d.m.Y H:i:s'),
            trim($view->render()),
        );
    }

    /**
     * @test
     * @dataProvider renderStaticReturnsHumanReadableDateDataProvider
     * @param non-empty-string $expected
     */
    public function renderStaticReturnsHumanReadableDate(DateTimeInterface $date, string $expected): void
    {
        $view = $this->createView('<s:date date="{date}" readable="1" />');
        $view->assign('date', $date);

        self::assertStringEndsWith($expected, trim($view->render()));
    }

    /**
     * @return Generator<string, array{DateTimeInterface, non-empty-string}>
     */
    public static function renderStaticReturnsHumanReadableDateDataProvider(): Generator
    {
        $format = static fn (int $interval, string $unit): array => [
            new DateTimeImmutable($interval . ' ' . $unit . ' ago'),
            $unit . ' ago',
        ];

        yield 'now' => [new Tests\Functional\Fixtures\LazyDateTime(), 'a few moments ago'];
        yield 'seconds ago' => [...$format(20, 'seconds')];
        yield 'minute ago' => [...$format(1, 'minute')];
        yield 'minutes ago' => [...$format(5, 'minutes')];
        yield 'hour ago' => [...$format(1, 'hour')];
        yield 'hours ago' => [...$format(5, 'hours')];
        yield 'day ago' => [...$format(1, 'day')];
        yield 'days ago' => [...$format(5, 'days')];

        $oldDate = DateTimeImmutable::createFromFormat('!Y-m-d', '2012-12-12');

        self::assertNotFalse($oldDate);

        yield 'more than a week' => [$oldDate, '12.12.2012'];
    }
}
