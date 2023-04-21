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

    /**
     * @var array<string, mixed>
     */
    private array $listResponse;

    protected function setUp(): void
    {
        $this->mockHandler = new Handler\MockHandler();

        $command = new Src\Command\ListModelsCommand(
            OpenAI::factory()->withHttpClient(new Client(['handler' => $this->mockHandler]))->make(),
        );

        $this->commandTester = new Console\Tester\CommandTester($command);
        $this->listResponse = $this->createListResponse();
    }

    /**
     * @test
     */
    public function executeListsAllGPTModels(): void
    {
        $response = new Psr7\Response(headers: ['Content-Type' => 'application/json']);
        $response->getBody()->write(json_encode($this->listResponse, JSON_THROW_ON_ERROR));
        $response->getBody()->rewind();

        $this->mockHandler->append($response);

        $this->commandTester->execute([]);

        self::assertStringContainsString(
            'Available GPT models',
            $this->commandTester->getDisplay(),
        );
        self::assertStringNotContainsString(
            implode(PHP_EOL, [
                ' * baz-1 (created at 01/01/2022)',
                ' * foo-1 (created at 01/01/2023)',
            ]),
            $this->commandTester->getDisplay(),
        );
        self::assertStringContainsString(
            implode(PHP_EOL, [
                ' * gpt-3.5 (created at 28/02/2023)',
                ' * gpt-3.5-turbo-0301 (created at 01/03/2023)',
            ]),
            $this->commandTester->getDisplay(),
        );
    }

    /**
     * @test
     */
    public function executeListsAllAvailableModels(): void
    {
        $response = new Psr7\Response(headers: ['Content-Type' => 'application/json']);
        $response->getBody()->write(json_encode($this->listResponse, JSON_THROW_ON_ERROR));
        $response->getBody()->rewind();

        $this->mockHandler->append($response);

        $this->commandTester->execute([
            '--all' => true,
        ]);

        self::assertStringContainsString(
            'Available OpenAI models',
            $this->commandTester->getDisplay(),
        );
        self::assertStringContainsString(
            implode(PHP_EOL, [
                ' * baz-1 (created at 01/01/2022)',
                ' * foo-1 (created at 01/01/2023)',
                ' * gpt-3.5 (created at 28/02/2023)',
                ' * gpt-3.5-turbo-0301 (created at 01/03/2023)',
            ]),
            $this->commandTester->getDisplay(),
        );
        self::assertStringContainsString(
            'Only GPT models can be used with this extension.',
            $this->commandTester->getDisplay(),
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function createListResponse(): array
    {
        $defaults = [
            'object' => 'object',
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

        return [
            'object' => 'object',
            'data' => [
                [
                    'id' => 'foo-1',
                    // 01/01/2023
                    'created' => 1672574400,
                    ...$defaults,
                ],
                [
                    'id' => 'gpt-3.5',
                    // 28/02/2023
                    'created' => 1677585600,
                    ...$defaults,
                ],
                [
                    'id' => 'gpt-3.5-turbo-0301',
                    // 01/03/2023
                    'created' => 1677672000,
                    ...$defaults,
                ],
                [
                    'id' => 'baz-1',
                    // 01/01/2022
                    'created' => 1641038400,
                    ...$defaults,
                ],
            ],
        ];
    }
}
