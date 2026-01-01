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

namespace EliasHaeussler\Typo3Solver\Tests\Functional\ProblemSolving\Solution\Prompt;

use EliasHaeussler\Typo3Solver as Src;
use PHPUnit\Framework;
use TYPO3\CMS\Core;
use TYPO3\TestingFramework;

/**
 * DefaultPromptTest
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
#[Framework\Attributes\CoversClass(Src\ProblemSolving\Solution\Prompt\DefaultPrompt::class)]
final class DefaultPromptTest extends TestingFramework\Core\Functional\FunctionalTestCase
{
    private Src\ProblemSolving\Solution\Prompt\DefaultPrompt $subject;
    private Core\Database\ConnectionPool $connectionPool;

    protected function setUp(): void
    {
        parent::setUp();

        $this->connectionPool = $this->get(Core\Database\ConnectionPool::class);
        $this->subject = new Src\ProblemSolving\Solution\Prompt\DefaultPrompt(
            new Src\View\TemplateRenderer(),
            $this->connectionPool,
        );
    }

    #[Framework\Attributes\Test]
    public function generateReturnsGeneratedPrompt(): void
    {
        $exception = new \Exception('Something went wrong.', 1680791875);
        $typo3Version = (new Core\Information\Typo3Version())->getVersion();
        $dbVersion = $this->connectionPool->getConnectionByName(
            Core\Database\ConnectionPool::DEFAULT_CONNECTION_NAME,
        )->getServerVersion();

        $actual = $this->subject->generate($exception);

        self::assertStringContainsString('Exception: "Something went wrong."', $actual);
        self::assertStringContainsString('* TYPO3 CMS ' . $typo3Version . ' (classic mode)', $actual);
        self::assertStringContainsString('* PHP ' . PHP_VERSION, $actual);
        self::assertStringContainsString('* ' . $dbVersion, $actual);
    }
}
