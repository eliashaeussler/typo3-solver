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

namespace EliasHaeussler\Typo3Solver\Tests\Unit\ProblemSolving\Solution\Provider;

use EliasHaeussler\Typo3Solver as Src;
use EliasHaeussler\Typo3Solver\Tests;
use GuzzleHttp\Client;
use GuzzleHttp\Handler;
use GuzzleHttp\Psr7;
use OpenAI;
use PHPUnit\Framework;
use TYPO3\TestingFramework;

/**
 * OpenAISolutionProviderTest
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
#[Framework\Attributes\CoversClass(Src\ProblemSolving\Solution\Provider\OpenAISolutionProvider::class)]
final class OpenAISolutionProviderTest extends TestingFramework\Core\Unit\UnitTestCase
{
    private Handler\MockHandler $mockHandler;
    private OpenAI\Client $client;
    private Src\ProblemSolving\Solution\Provider\OpenAISolutionProvider $subject;
    private Src\ProblemSolving\Problem\Problem $problem;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockHandler = new Handler\MockHandler();
        $this->client = \OpenAI::factory()->withHttpClient(new Client(['handler' => $this->mockHandler]))->make();
        $this->subject = new Src\ProblemSolving\Solution\Provider\OpenAISolutionProvider(
            new Src\Configuration\Configuration(),
            $this->client,
        );
        $this->problem = Tests\Unit\DataProvider\ProblemDataProvider::get(solutionProvider: $this->subject);

        // Configure API key
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][Src\Extension::KEY]['api']['key'] = 'foo';
    }

    #[Framework\Attributes\Test]
    public function createReturnsInitializedProvider(): void
    {
        self::assertEquals(
            $this->subject,
            Src\ProblemSolving\Solution\Provider\OpenAISolutionProvider::create($this->client),
        );
    }

    #[Framework\Attributes\Test]
    public function getSolutionThrowsExceptionIfApiKeyIsNotConfigured(): void
    {
        unset($GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][Src\Extension::KEY]['api']['key']);

        $this->expectExceptionObject(
            Src\Exception\ApiKeyMissingException::create(),
        );

        $this->subject->getSolution($this->problem);
    }

    #[Framework\Attributes\Test]
    public function getSolutionThrowsExceptionIfRequestFails(): void
    {
        $this->expectExceptionObject(
            Src\Exception\UnableToSolveException::create($this->problem),
        );

        $this->mockHandler->append(new \Exception());

        $this->subject->getSolution($this->problem);
    }

    #[Framework\Attributes\Test]
    public function getSolutionReturnsSolutionFromClientResponse(): void
    {
        $payload = OpenAI\Responses\Chat\CreateResponse::fake([
            'model' => 'model',
            'choices' => [
                [
                    'message' => [
                        'role' => 'role',
                        'content' => 'content',
                    ],
                ],
            ],
        ])->toArray();
        $response = new Psr7\Response(headers: [
            'Content-Type' => 'application/json',
            'x-request-id' => 'foo',
            'openai-processing-ms' => '0',
            'openai-version' => '1',
        ]);
        $response->getBody()->write(\json_encode($payload, JSON_THROW_ON_ERROR));
        $response->getBody()->rewind();

        $this->mockHandler->append($response);

        self::assertEquals(
            Tests\Unit\DataProvider\SolutionDataProvider::get(message: 'content'),
            $this->subject->getSolution($this->problem),
        );
    }

    #[Framework\Attributes\Test]
    public function getStreamedSolutionThrowsExceptionIfApiKeyIsNotConfigured(): void
    {
        unset($GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][Src\Extension::KEY]['api']['key']);

        $this->expectExceptionObject(
            Src\Exception\ApiKeyMissingException::create(),
        );

        \iterator_to_array($this->subject->getStreamedSolution($this->problem));
    }

    #[Framework\Attributes\Test]
    public function getStreamedSolutionThrowsExceptionIfRequestFails(): void
    {
        $this->expectExceptionObject(
            Src\Exception\UnableToSolveException::create($this->problem),
        );

        $this->mockHandler->append(new \Exception());

        \iterator_to_array($this->subject->getStreamedSolution($this->problem));
    }

    #[Framework\Attributes\Test]
    public function getStreamedSolutionReturnsSolutionFromClientResponse(): void
    {
        $streamedResponse1 = [
            'id' => 'id',
            'object' => 'object',
            'created' => 123,
            'model' => 'model',
            'choices' => [
                [
                    'index' => 0,
                    'delta' => [
                        'role' => 'role',
                        'content' => 'content 1',
                    ],
                    'finish_reason' => 'stop',
                ],
            ],
        ];
        $streamedResponse2 = [
            'id' => 'id',
            'object' => 'object',
            'created' => 123,
            'model' => 'model',
            'choices' => [
                [
                    'index' => 0,
                    'delta' => [
                        'role' => 'role',
                        'content' => ' ... content 2',
                    ],
                    'finish_reason' => 'stop',
                ],
            ],
        ];

        $response = new Psr7\Response();
        $response->getBody()->write('data: ' . \json_encode($streamedResponse1) . PHP_EOL);
        $response->getBody()->write('data: ' . \json_encode($streamedResponse2) . PHP_EOL);
        $response->getBody()->rewind();

        $this->mockHandler->append($response);

        $expected = [
            Tests\Unit\DataProvider\SolutionDataProvider::get(message: 'content 1'),
            Tests\Unit\DataProvider\SolutionDataProvider::get(message: 'content 1 ... content 2'),
        ];

        self::assertEquals($expected, \iterator_to_array($this->subject->getStreamedSolution($this->problem)));
    }

    #[Framework\Attributes\Test]
    public function canBeUsedChecksIfIgnoredExceptionCodesAreConfigured(): void
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][Src\Extension::KEY]['ignoredCodes'] = '123';

        self::assertFalse($this->subject->canBeUsed($this->problem->getException()));

        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][Src\Extension::KEY]['ignoredCodes'] = '';

        self::assertTrue($this->subject->canBeUsed($this->problem->getException()));
    }

    #[Framework\Attributes\Test]
    public function isCacheableReturnsTrue(): void
    {
        self::assertTrue($this->subject->isCacheable());
    }

    #[Framework\Attributes\Test]
    public function listModelsReturnsListOfSupportedModels(): void
    {
        $response = new Psr7\Response(headers: [
            'Content-Type' => 'application/json',
            'x-request-id' => 'foo',
            'openai-processing-ms' => '0',
            'openai-version' => '1',
        ]);
        $response->getBody()->write(\json_encode($this->createListResponse(), JSON_THROW_ON_ERROR));
        $response->getBody()->rewind();

        $this->mockHandler->append($response);

        $expected = [
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

        self::assertEquals($expected, $this->subject->listModels());
    }

    #[Framework\Attributes\Test]
    public function listModelsReturnsListOfAllAvailableModels(): void
    {
        $response = new Psr7\Response(headers: [
            'Content-Type' => 'application/json',
            'x-request-id' => 'foo',
            'openai-processing-ms' => '0',
            'openai-version' => '1',
        ]);
        $response->getBody()->write(\json_encode($this->createListResponse(), JSON_THROW_ON_ERROR));
        $response->getBody()->rewind();

        $this->mockHandler->append($response);

        $expected = [
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

        self::assertEquals($expected, $this->subject->listModels(true));
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
                [
                    'id' => 'gpt-4o-realtime-preview',
                    // 30/09/2024
                    'created' => 1727654400,
                    ...$defaults,
                ],
                [
                    'id' => 'gpt-4o-mini-audio-preview',
                    // 16/12/2024
                    'created' => 1734307200,
                    ...$defaults,
                ],
            ],
        ];
    }

    private function createDate(int $timestamp): \DateTimeImmutable
    {
        return new \DateTimeImmutable('@' . $timestamp);
    }
}
