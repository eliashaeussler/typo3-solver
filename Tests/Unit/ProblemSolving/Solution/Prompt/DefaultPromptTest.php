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

namespace EliasHaeussler\Typo3Solver\Tests\Unit\ProblemSolving\Solution\Prompt;

use EliasHaeussler\Typo3Solver as Src;
use Exception;
use TYPO3\CMS\Core;
use TYPO3\TestingFramework;

/**
 * DefaultPromptTest
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
final class DefaultPromptTest extends TestingFramework\Core\Unit\UnitTestCase
{
    private Src\ProblemSolving\Solution\Prompt\DefaultPrompt $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new Src\ProblemSolving\Solution\Prompt\DefaultPrompt(new Src\View\TemplateRenderer());
    }

    /**
     * @test
     */
    public function generateReturnsGeneratedPrompt(): void
    {
        $exception = new Exception('Something went wrong.', 1680791875);
        $typo3Version = (new Core\Information\Typo3Version())->getVersion();

        $actual = $this->subject->generate($exception);

        self::assertStringContainsString('Exception: "Something went wrong."', $actual);
        self::assertStringContainsString(
            'Please note that this TYPO3 CMS installation is in classic (symlink) mode and using version ' . $typo3Version,
            $actual,
        );
        self::assertStringContainsString('The PHP version being used is ' . PHP_VERSION, $actual);
    }
}
