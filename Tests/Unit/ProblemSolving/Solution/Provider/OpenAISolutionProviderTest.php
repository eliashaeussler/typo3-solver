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
        $payload = [
            'id' => 'id',
            'object' => 'object',
            'created' => 123,
            'model' => 'model',
            'choices' => [
                [
                    'index' => 0,
                    'message' => [
                        'role' => 'role',
                        'content' => 'content',
                    ],
                    'finish_reason' => null,
                ],
            ],
            'usage' => [
                'prompt_tokens' => 123,
                'completion_tokens' => 123,
                'total_tokens' => 123,
            ],
        ];
        $response = new Psr7\Response(headers: [
            'Content-Type' => 'application/json',
            'x-request-id' => 'foo',
            'openai-processing-ms' => '0',
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
                    'finish_reason' => null,
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
                    'finish_reason' => null,
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
}
