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

namespace EliasHaeussler\Typo3Solver\Tests\Unit\Command;

use EliasHaeussler\Typo3Solver as Src;
use EliasHaeussler\Typo3Solver\Tests;
use PHPUnit\Framework;
use Symfony\Component\Console;
use TYPO3\TestingFramework;

/**
 * ListModelsCommandTest
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
#[Framework\Attributes\CoversClass(Src\Command\ListModelsCommand::class)]
final class ListModelsCommandTest extends TestingFramework\Core\Unit\UnitTestCase
{
    private Tests\Unit\Fixtures\DummySolutionProvider $solutionProvider;
    private Console\Tester\CommandTester $commandTester;

    protected function setUp(): void
    {
        parent::setUp();

        $this->solutionProvider = Tests\Unit\Fixtures\DummySolutionProvider::create();
        $this->commandTester = new Console\Tester\CommandTester(
            new Src\Command\ListModelsCommand(
                new Src\Configuration\Configuration(),
                $this->solutionProvider,
            ),
        );
    }

    #[Framework\Attributes\Test]
    public function executeListsAllSupportedModels(): void
    {
        $this->solutionProvider->models = [
            new Src\ProblemSolving\Solution\Provider\Model\AiModel(
                'gpt-3.5',
                // 28/02/2023
                $this->createDate(1677585600),
            ),
            new Src\ProblemSolving\Solution\Provider\Model\AiModel(
                'gpt-3.5-turbo-0301',
                // 01/03/2023
                $this->createDate(1677672000),
            ),
        ];

        $this->commandTester->execute([]);

        self::assertStringContainsString(
            'Supported AI models',
            $this->commandTester->getDisplay(),
        );
        self::assertStringNotContainsString(
            \implode(PHP_EOL, [
                ' * baz-1 (created at 01/01/2022)',
                ' * foo-1 (created at 01/01/2023)',
            ]),
            $this->commandTester->getDisplay(),
        );
        self::assertStringNotContainsString(
            \implode(PHP_EOL, [
                ' * gpt-4o-mini-audio-preview (created at 16/12/2024)',
                ' * gpt-4o-realtime-preview (created at 30/09/2024)',
            ]),
            $this->commandTester->getDisplay(),
        );
        self::assertStringContainsString(
            \implode(PHP_EOL, [
                ' * gpt-3.5 (created at 28/02/2023)',
                ' * gpt-3.5-turbo-0301 (created at 01/03/2023)',
            ]),
            $this->commandTester->getDisplay(),
        );
    }

    #[Framework\Attributes\Test]
    public function executeListsAllAvailableModels(): void
    {
        $this->solutionProvider->models = [
            new Src\ProblemSolving\Solution\Provider\Model\AiModel(
                'foo-1',
                // 01/01/2023
                $this->createDate(1672574400),
            ),
            new Src\ProblemSolving\Solution\Provider\Model\AiModel(
                'gpt-3.5',
                // 28/02/2023
                $this->createDate(1677585600),
            ),
            new Src\ProblemSolving\Solution\Provider\Model\AiModel(
                'gpt-3.5-turbo-0301',
                // 01/03/2023
                $this->createDate(1677672000),
            ),
            new Src\ProblemSolving\Solution\Provider\Model\AiModel(
                'baz-1',
                // 01/01/2022
                $this->createDate(1641038400),
            ),
            new Src\ProblemSolving\Solution\Provider\Model\AiModel(
                'gpt-4o-realtime-preview',
                // 30/09/2024
                $this->createDate(1727654400),
            ),
            new Src\ProblemSolving\Solution\Provider\Model\AiModel(
                'gpt-4o-mini-audio-preview',
                // 16/12/2024
                $this->createDate(1734307200),
            ),
        ];

        $this->commandTester->execute([
            '--all' => true,
        ]);

        self::assertStringContainsString(
            'Available AI models',
            $this->commandTester->getDisplay(),
        );
        self::assertStringContainsString(
            \implode(PHP_EOL, [
                ' * baz-1 (created at 01/01/2022)',
                ' * foo-1 (created at 01/01/2023)',
                ' * gpt-3.5 (created at 28/02/2023)',
                ' * gpt-3.5-turbo-0301 (created at 01/03/2023)',
                ' * gpt-4o-mini-audio-preview (created at 16/12/2024)',
                ' * gpt-4o-realtime-preview (created at 30/09/2024)',
            ]),
            $this->commandTester->getDisplay(),
        );
        self::assertStringContainsString(
            'Only a limited set of models can be used with this extension.',
            $this->commandTester->getDisplay(),
        );
    }

    private function createDate(int $timestamp): \DateTimeImmutable
    {
        return new \DateTimeImmutable('@' . $timestamp);
    }
}
