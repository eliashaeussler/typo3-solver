<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS extension "solver".
 *
 * Copyright (C) 2023-2026 Elias Häußler <elias@haeussler.dev>
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

namespace EliasHaeussler\Typo3Solver\Tests\Unit\ProblemSolving\Problem;

use EliasHaeussler\Typo3Solver as Src;
use EliasHaeussler\Typo3Solver\Tests;
use PHPUnit\Framework;
use TYPO3\TestingFramework;

/**
 * ProblemTest
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
#[Framework\Attributes\CoversClass(Src\ProblemSolving\Problem\Problem::class)]
final class ProblemTest extends TestingFramework\Core\Unit\UnitTestCase
{
    private \Exception $exception;
    private Tests\Unit\Fixtures\DummySolutionProvider $provider;
    private Src\ProblemSolving\Problem\Problem $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->exception = new \Exception();
        $this->provider = new Tests\Unit\Fixtures\DummySolutionProvider();
        $this->subject = new Src\ProblemSolving\Problem\Problem($this->exception, $this->provider, 'foo');
    }

    #[Framework\Attributes\Test]
    public function getExceptionReturnsException(): void
    {
        self::assertSame($this->exception, $this->subject->getException());
    }

    #[Framework\Attributes\Test]
    public function getSolutionProviderReturnsSolutionProvider(): void
    {
        self::assertSame($this->provider, $this->subject->getSolutionProvider());
    }

    #[Framework\Attributes\Test]
    public function getPromptReturnsPrompt(): void
    {
        self::assertSame('foo', $this->subject->getPrompt());
    }
}
