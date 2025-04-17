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

namespace EliasHaeussler\Typo3Solver\Tests\Unit\ProblemSolving\Solution\Model;

use EliasHaeussler\Typo3Solver as Src;
use EliasHaeussler\Typo3Solver\Tests;
use GeminiAPI\Enums;
use GeminiAPI\Resources;
use OpenAI\Responses;
use PHPUnit\Framework;
use TYPO3\TestingFramework;

/**
 * CompletionResponseTest
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
#[Framework\Attributes\CoversClass(Src\ProblemSolving\Solution\Model\CompletionResponse::class)]
final class CompletionResponseTest extends TestingFramework\Core\Unit\UnitTestCase
{
    private Src\ProblemSolving\Solution\Model\CompletionResponse $subject;

    public function setUp(): void
    {
        parent::setUp();

        $this->subject = new Src\ProblemSolving\Solution\Model\CompletionResponse(
            0,
            new Src\ProblemSolving\Solution\Model\Message('model', 'foo'),
            'MAX_TOKENS',
        );
    }

    #[Framework\Attributes\Test]
    public function fromOpenAIChoiceReturnsCompletionResponseFromCreateResponseChoice(): void
    {
        $choice = Responses\Chat\CreateResponseChoice::from([
            'index' => 0,
            'message' => [
                'role' => 'model',
                'content' => 'foo',
                'tool_calls' => null,
                'function_call' => null,
            ],
            'finish_reason' => 'MAX_TOKENS',
        ]);

        self::assertEquals(
            $this->subject,
            Src\ProblemSolving\Solution\Model\CompletionResponse::fromOpenAIChoice($choice),
        );
    }

    #[Framework\Attributes\Test]
    public function fromOpenAIChoiceReturnsCompletionResponseFromCreateStreamedResponseChoice(): void
    {
        $choice = Responses\Chat\CreateStreamedResponseChoice::from([
            'index' => 0,
            'delta' => [
                'role' => 'model',
                'content' => 'foo',
            ],
            'finish_reason' => 'MAX_TOKENS',
        ]);

        self::assertEquals(
            $this->subject,
            Src\ProblemSolving\Solution\Model\CompletionResponse::fromOpenAIChoice($choice),
        );
    }

    #[Framework\Attributes\Test]
    public function fromGeminiCandidateReturnsCompletionResponseFromGeminiCandidate(): void
    {
        $candidate = $this->createMock(Resources\Candidate::class);

        Tests\Unit\DataProvider\GeminiDataProvider::fillCandidateProperties($candidate);

        self::assertEquals(
            $this->subject,
            Src\ProblemSolving\Solution\Model\CompletionResponse::fromGeminiCandidate($candidate),
        );
    }

    #[Framework\Attributes\Test]
    public function fromGeminiCandidateIgnoresNonTextContentParts(): void
    {
        $candidate = $this->createMock(Resources\Candidate::class);

        Tests\Unit\DataProvider\GeminiDataProvider::fillCandidateProperties(
            mock: $candidate,
            part: new Resources\Parts\FilePart(Enums\MimeType::TEXT_MARKDOWN, 'foo'),
        );

        $expected = new Src\ProblemSolving\Solution\Model\CompletionResponse(
            0,
            new Src\ProblemSolving\Solution\Model\Message('model', null),
            'MAX_TOKENS',
        );

        self::assertEquals(
            $expected,
            Src\ProblemSolving\Solution\Model\CompletionResponse::fromGeminiCandidate($candidate),
        );
    }

    #[Framework\Attributes\Test]
    public function fromArrayReturnsCompletionResponseFromArray(): void
    {
        $response = [
            'index' => 0,
            'message' => [
                'role' => 'model',
                'content' => 'foo',
            ],
            'finishReason' => 'MAX_TOKENS',
        ];

        self::assertEquals(
            $this->subject,
            Src\ProblemSolving\Solution\Model\CompletionResponse::fromArray($response),
        );
    }

    #[Framework\Attributes\Test]
    public function mergeMergesResponseWithOtherResponse(): void
    {
        $other = new Src\ProblemSolving\Solution\Model\CompletionResponse(
            0,
            new Src\ProblemSolving\Solution\Model\Message('model', 'baz'),
            'otherFinishReason',
        );

        $expected = new Src\ProblemSolving\Solution\Model\CompletionResponse(
            0,
            new Src\ProblemSolving\Solution\Model\Message('model', 'foobaz'),
            'otherFinishReason',
        );

        self::assertEquals($expected, $this->subject->merge($other));
    }

    #[Framework\Attributes\Test]
    public function isFinishedReturnsTrueIfFinishReasonIsGiven(): void
    {
        self::assertTrue($this->subject->isFinished());

        $subject = new Src\ProblemSolving\Solution\Model\CompletionResponse(
            0,
            new Src\ProblemSolving\Solution\Model\Message('model', 'foo'),
        );

        self::assertFalse($subject->isFinished());
    }

    #[Framework\Attributes\Test]
    public function toArrayReturnsArrayRepresentation(): void
    {
        $expected = [
            'index' => 0,
            'message' => [
                'role' => 'model',
                'content' => 'foo',
            ],
            'finishReason' => 'MAX_TOKENS',
        ];

        self::assertSame($expected, $this->subject->toArray());
    }

    #[Framework\Attributes\Test]
    public function subjectIsJsonSerializable(): void
    {
        $expected = [
            'index' => 0,
            'message' => [
                'role' => 'model',
                'content' => 'foo',
            ],
            'finishReason' => 'MAX_TOKENS',
        ];

        self::assertJsonStringEqualsJsonString(
            \json_encode($expected, JSON_THROW_ON_ERROR),
            \json_encode($this->subject, JSON_THROW_ON_ERROR),
        );
    }
}
