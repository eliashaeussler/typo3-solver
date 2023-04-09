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

namespace EliasHaeussler\Typo3Solver\Tests\Unit\Formatter;

use DateTimeImmutable;
use EliasHaeussler\Typo3Solver as Src;
use PHPUnit\Framework;
use Symfony\Component\Console;
use TYPO3\TestingFramework;

use function trim;

/**
 * CliFormatterTest
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
final class CliFormatterTest extends TestingFramework\Core\Unit\UnitTestCase
{
    private Src\Formatter\CliFormatter $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new Src\Formatter\CliFormatter();
    }

    #[Framework\Attributes\Test]
    public function formatFormatsSolution(): void
    {
        $now = new DateTimeImmutable();

        $problem = Src\Tests\Unit\DataProvider\ProblemDataProvider::get();
        $solution = Src\Tests\Unit\DataProvider\SolutionDataProvider::get(3);
        $solution->setCreateDate($now);
        $solution->setCacheIdentifier('foo');

        $expected = <<<'TEXT'
Solution #1
===========
message 1

Solution #2
===========
message 2

Solution #3
===========
message 3

Cache
=====
This solution was cached a few moments ago as foo
TEXT;

        self::assertSame(trim($expected), $this->subject->format($problem, $solution));
    }

    #[Framework\Attributes\Test]
    public function formatIncludesPromptIfOutputIsVerbose(): void
    {
        $output = new Console\Output\BufferedOutput();
        $output->setVerbosity(Console\Output\OutputInterface::VERBOSITY_VERBOSE);

        $this->subject->setOutput($output);

        $problem = Src\Tests\Unit\DataProvider\ProblemDataProvider::get();
        $solution = Src\Tests\Unit\DataProvider\SolutionDataProvider::get();

        $expected = <<<'TEXT'
Prompt
======
prompt
TEXT;

        self::assertStringContainsString($expected, $this->subject->format($problem, $solution));
    }
}
