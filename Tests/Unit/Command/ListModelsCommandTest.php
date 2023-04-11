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
use GuzzleHttp\Client;
use GuzzleHttp\Handler;
use GuzzleHttp\Psr7;
use OpenAI;
use Symfony\Component\Console;
use TYPO3\TestingFramework;

use function implode;
use function json_encode;

/**
 * ListModelsCommandTest
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
final class ListModelsCommandTest extends TestingFramework\Core\Unit\UnitTestCase
{
    private Handler\MockHandler $mockHandler;
    private Console\Tester\CommandTester $commandTester;

    protected function setUp(): void
    {
        $this->mockHandler = new Handler\MockHandler();

        $command = new Src\Command\ListModelsCommand(
            OpenAI::factory()->withHttpClient(new Client(['handler' => $this->mockHandler]))->make(),
        );

        $this->commandTester = new Console\Tester\CommandTester($command);
    }

    /**
     * @test
     */
    public function executeListsAllAvailableModels(): void
    {
        $defaults = [
            'object' => 'object',
            'created' => 123,
            'owned_by' => 'owned_by',
            'permission' => [
                [
                    'id' => 'id',
                    'object' => 'object',
                    'created' => 123,
                    'allow_create_engine' => true,
                    'allow_sampling' => true,
                    'allow_logprobs' => true,
                    'allow_search_indices' => true,
                    'allow_view' => true,
                    'allow_fine_tuning' => true,
                    'organization' => 'organization',
                    'group' => 'group',
                    'is_blocking' => true,
                ],
            ],
            'root' => 'root',
            'parent' => 'parent',
        ];

        $response = new Psr7\Response(headers: ['Content-Type' => 'application/json']);
        $response->getBody()->write(json_encode([
            'object' => 'object',
            'data' => [
                [
                    'id' => 'foo-1',
                    ...$defaults,
                ],
                [
                    'id' => 'foo-2',
                    ...$defaults,
                ],
                [
                    'id' => 'baz-2',
                    ...$defaults,
                ],
                [
                    'id' => 'baz-1',
                    ...$defaults,
                ],
            ],
        ], JSON_THROW_ON_ERROR));
        $response->getBody()->rewind();

        $this->mockHandler->append($response);

        $this->commandTester->execute([]);

        self::assertStringContainsString(
            implode(PHP_EOL, [
                ' * baz-1',
                ' * baz-2',
                ' * foo-1',
                ' * foo-2',
            ]),
            $this->commandTester->getDisplay(),
        );
    }
}
