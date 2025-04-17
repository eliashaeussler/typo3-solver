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
use GeminiAPI\Client;
use GuzzleHttp\Handler;
use GuzzleHttp\Psr7;
use PHPUnit\Framework;
use TYPO3\TestingFramework;

/**
 * GeminiSolutionProviderTest
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
#[Framework\Attributes\CoversClass(Src\ProblemSolving\Solution\Provider\GeminiSolutionProvider::class)]
final class GeminiSolutionProviderTest extends TestingFramework\Core\Unit\UnitTestCase
{
    private Handler\MockHandler $mockHandler;
    private Client $client;
    private Src\ProblemSolving\Solution\Provider\GeminiSolutionProvider $subject;
    private Src\ProblemSolving\Problem\Problem $problem;

    public function setUp(): void
    {
        parent::setUp();

        $this->mockHandler = new Handler\MockHandler();
        $this->client = new Client(
            'foo',
            new \GuzzleHttp\Client(['handler' => $this->mockHandler]),
        );
        $this->subject = new Src\ProblemSolving\Solution\Provider\GeminiSolutionProvider(
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
            Src\ProblemSolving\Solution\Provider\GeminiSolutionProvider::create($this->client),
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
            'promptFeedback' => [
                'blockReason' => null,
            ],
            'candidates' => [
                [
                    'citationMetadata' => [
                        'citationSources' => [],
                    ],
                    'safetyRatings' => [],
                    'content' => [
                        'parts' => [
                            [
                                'text' => 'content',
                                'inlineData' => [
                                    'mimeType' => 'text/plain',
                                    'data' => 'content',
                                ],
                            ],
                        ],
                        'role' => 'model',
                    ],
                    'finishReason' => 'MAX_TOKENS',
                    'tokenCount' => 0,
                    'index' => 0,
                ],
            ],
        ];
        $response = new Psr7\Response(headers: [
            'Content-Type' => 'application/json',
        ]);
        $response->getBody()->write(\json_encode($payload, JSON_THROW_ON_ERROR));
        $response->getBody()->rewind();

        $this->mockHandler->append($response);

        self::assertEquals(
            Tests\Unit\DataProvider\SolutionDataProvider::get(
                1,
                'content',
                'model',
                'MAX_TOKENS',
                'gpt-4o-mini',
            ),
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
}
