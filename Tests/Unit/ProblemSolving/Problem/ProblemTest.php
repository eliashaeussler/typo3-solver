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

namespace EliasHaeussler\Typo3Solver\Tests\Unit\ProblemSolving\Problem;

use EliasHaeussler\Typo3Solver as Src;
use EliasHaeussler\Typo3Solver\Tests;
use Exception;
use TYPO3\TestingFramework;

/**
 * ProblemTest
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 */
final class ProblemTest extends TestingFramework\Core\Unit\UnitTestCase
{
    private Exception $exception;
    private Tests\Unit\Fixtures\DummySolutionProvider $provider;
    private Src\ProblemSolving\Problem\Problem $subject;

    protected function setUp(): void
    {
        $this->exception = new Exception();
        $this->provider = new Tests\Unit\Fixtures\DummySolutionProvider();
        $this->subject = new Src\ProblemSolving\Problem\Problem($this->exception, $this->provider, 'foo');
    }

    /**
     * @test
     */
    public function getExceptionReturnsException(): void
    {
        self::assertSame($this->exception, $this->subject->getException());
    }

    /**
     * @test
     */
    public function getSolutionProviderReturnsSolutionProvider(): void
    {
        self::assertSame($this->provider, $this->subject->getSolutionProvider());
    }

    /**
     * @test
     */
    public function getPromptReturnsPrompt(): void
    {
        self::assertSame('foo', $this->subject->getPrompt());
    }
}
