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

namespace EliasHaeussler\Typo3Solver\Tests\Unit\ProblemSolving\Solution\Provider;

use EliasHaeussler\Typo3Solver as Src;
use EliasHaeussler\Typo3Solver\Tests;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Handler;
use GuzzleHttp\Psr7;
use OpenAI;
use TYPO3\TestingFramework;

use function iterator_to_array;
use function json_encode;

/**
 * OpenAISolutionProviderTest
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
final class OpenAISolutionProviderTest extends TestingFramework\Core\Unit\UnitTestCase
{
    private Handler\MockHandler $mockHandler;
    private Src\ProblemSolving\Solution\Provider\OpenAISolutionProvider $subject;
    private Src\ProblemSolving\Problem\Problem $problem;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockHandler = new Handler\MockHandler();

        $client = OpenAI::factory()
            ->withHttpClient(new Client(['handler' => $this->mockHandler]))
            ->make()
        ;

        $this->subject = new Src\ProblemSolving\Solution\Provider\OpenAISolutionProvider($client);
        $this->problem = Tests\Unit\DataProvider\ProblemDataProvider::get(solutionProvider: $this->subject);

        // Configure API key
        /* @phpstan-ignore-next-line */
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][Src\Extension::KEY]['api']['key'] = 'foo';
    }

    /**
     * @test
     */
    public function getSolutionThrowsExceptionIfApiKeyIsNotConfigured(): void
    {
        /* @phpstan-ignore-next-line */
        unset($GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][Src\Extension::KEY]['api']['key']);

        $this->expectExceptionObject(
            Src\Exception\ApiKeyMissingException::create(),
        );

        $this->subject->getSolution($this->problem);
    }

    /**
     * @test
     */
    public function getSolutionThrowsExceptionIfRequestFails(): void
    {
        $this->expectExceptionObject(
            Src\Exception\UnableToSolveException::create($this->problem),
        );

        $this->mockHandler->append(new Exception());

        $this->subject->getSolution($this->problem);
    }

    /**
     * @test
     */
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
        $response = new Psr7\Response(headers: ['Content-Type' => 'application/json']);
        $response->getBody()->write(json_encode($payload, JSON_THROW_ON_ERROR));
        $response->getBody()->rewind();

        $this->mockHandler->append($response);

        self::assertEquals(
            Tests\Unit\DataProvider\SolutionDataProvider::get(message: 'content'),
            $this->subject->getSolution($this->problem),
        );
    }

    /**
     * @test
     */
    public function getStreamedSolutionThrowsExceptionIfApiKeyIsNotConfigured(): void
    {
        /* @phpstan-ignore-next-line */
        unset($GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][Src\Extension::KEY]['api']['key']);

        $this->expectExceptionObject(
            Src\Exception\ApiKeyMissingException::create(),
        );

        iterator_to_array($this->subject->getStreamedSolution($this->problem));
    }

    /**
     * @test
     */
    public function getStreamedSolutionThrowsExceptionIfRequestFails(): void
    {
        $this->expectExceptionObject(
            Src\Exception\UnableToSolveException::create($this->problem),
        );

        $this->mockHandler->append(new Exception());

        iterator_to_array($this->subject->getStreamedSolution($this->problem));
    }

    /**
     * @test
     */
    public function getStreamedSolutionReturnsSolutionFromClientResponse(): void
    {
        $payload = [
            'id' => 'id',
            'object' => 'object',
            'created' => 123,
            'model' => 'model',
            'choices' => [
                [
                    'index' => 0,
                    'delta' => [
                        'role' => 'role',
                        'content' => 'content',
                    ],
                    'finish_reason' => null,
                ],
            ],
        ];
        $response = new Psr7\Response(headers: ['Content-Type' => 'application/json']);
        $response->getBody()->write('data: ' . json_encode($payload, JSON_THROW_ON_ERROR));
        $response->getBody()->rewind();

        $this->mockHandler->append($response);

        $expected = Tests\Unit\DataProvider\SolutionDataProvider::get(message: 'content');

        self::assertEquals([$expected], iterator_to_array($this->subject->getStreamedSolution($this->problem)));
    }

    /**
     * @test
     */
    public function canBeUsedChecksIfIgnoredExceptionCodesAreConfigured(): void
    {
        /* @phpstan-ignore-next-line */
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][Src\Extension::KEY]['ignoredCodes'] = '123';

        self::assertFalse($this->subject->canBeUsed($this->problem->getException()));

        /* @phpstan-ignore-next-line */
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][Src\Extension::KEY]['ignoredCodes'] = '';

        self::assertTrue($this->subject->canBeUsed($this->problem->getException()));
    }

    /**
     * @test
     */
    public function isCacheableReturnsTrue(): void
    {
        self::assertTrue($this->subject->isCacheable());
    }
}
