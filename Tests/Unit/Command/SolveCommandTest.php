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
 * SolveCommandTest
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
#[Framework\Attributes\CoversClass(Src\Command\SolveCommand::class)]
final class SolveCommandTest extends TestingFramework\Core\Unit\UnitTestCase
{
    private Src\Cache\ExceptionsCache $exceptionsCache;
    private Src\Cache\SolutionsCache $solutionsCache;
    private Tests\Unit\Fixtures\DummySolutionProvider $provider;
    private Src\ProblemSolving\Solution\Prompt\DefaultPrompt $prompt;
    private Console\Tester\CommandTester $commandTester;

    protected function setUp(): void
    {
        parent::setUp();

        $this->exceptionsCache = new Src\Cache\ExceptionsCache();
        $this->solutionsCache = new Src\Cache\SolutionsCache();
        $this->provider = Tests\Unit\Fixtures\DummySolutionProvider::create();
        $this->prompt = Src\ProblemSolving\Solution\Prompt\DefaultPrompt::create();

        $command = new Src\Command\SolveCommand(
            new Src\Configuration\Configuration(),
            $this->exceptionsCache,
            $this->solutionsCache,
            new Src\Formatter\CliFormatter(new Src\View\TemplateRenderer()),
            new Src\Formatter\JsonFormatter(),
            $this->provider,
        );

        $this->commandTester = new Console\Tester\CommandTester($command);

        $this->exceptionsCache->flush();
        $this->solutionsCache->flush();
    }

    #[Framework\Attributes\Test]
    public function executeThrowsExceptionIfConflictingParametersAreGiven(): void
    {
        $this->expectExceptionObject(
            Src\Exception\IOException::forConflictingParameters('problem', '--identifier'),
        );

        $this->commandTester->execute([
            'problem' => 'Something went wrong.',
            '--identifier' => 'foo',
        ]);
    }

    #[Framework\Attributes\Test]
    public function executeThrowsExceptionIfExceptionCacheEntryForGivenCacheIdentifierDoesNotExist(): void
    {
        $this->expectExceptionObject(
            Src\Exception\MissingCacheEntryException::create('foo'),
        );

        $this->commandTester->execute(['--identifier' => 'foo']);
    }

    #[Framework\Attributes\Test]
    public function executeProvidesSolutionForGivenExceptionIdentifier(): void
    {
        $exception = new \Exception('Something went wrong.', 123);
        $solution = Tests\Unit\DataProvider\SolutionDataProvider::get();

        $this->provider->solution = $solution;

        $identifier = $this->exceptionsCache->set($exception);

        $this->commandTester->execute(['--identifier' => $identifier]);

        self::assertSame(0, $this->commandTester->getStatusCode());
        self::assertIsString($solution->responses[0]->message->content);
        self::assertStringContainsString(
            $solution->responses[0]->message->content,
            $this->commandTester->getDisplay(),
        );
    }

    #[Framework\Attributes\Test]
    public function executeProvidesSolutionForGivenReconstructedProblem(): void
    {
        $exception = Src\Exception\CustomSolvableException::create(
            'Something went wrong.',
            123,
            __FILE__,
            __LINE__,
        );
        $solution = Tests\Unit\DataProvider\SolutionDataProvider::get();

        $this->provider->solution = $solution;

        $this->commandTester->execute([
            'problem' => $exception->getMessage(),
            '--code' => $exception->getCode(),
            '--file' => $exception->getFile(),
            '--line' => $exception->getLine(),
        ]);

        self::assertSame(0, $this->commandTester->getStatusCode());
        self::assertIsString($solution->responses[0]->message->content);
        self::assertStringContainsString(
            $solution->responses[0]->message->content,
            $this->commandTester->getDisplay(),
        );
    }

    #[Framework\Attributes\Test]
    public function executeThrowsExceptionIfRequiredParametersAreMissing(): void
    {
        $this->expectExceptionObject(
            Src\Exception\IOException::forMissingRequiredParameter('problem'),
        );

        $this->commandTester->execute([]);
    }

    #[Framework\Attributes\Test]
    public function executeRemovesCacheEntryWithRefreshOption(): void
    {
        $exception = Src\Exception\CustomSolvableException::create(
            'Something went wrong.',
            123,
            __FILE__,
            __LINE__,
        );
        $problem = new Src\ProblemSolving\Problem\Problem($exception, $this->provider, ($this->prompt)->generate($exception));
        $solution = Tests\Unit\DataProvider\SolutionDataProvider::get();

        $identifier = $this->exceptionsCache->set($exception);

        $this->solutionsCache->set($problem, $solution);

        self::assertNotNull($this->solutionsCache->get($problem));

        $this->commandTester->execute(['--identifier' => $identifier, '--refresh' => true]);

        self::assertSame(0, $this->commandTester->getStatusCode());
        self::assertNotEquals($solution->responses, $this->solutionsCache->get($problem)->responses);
    }

    #[Framework\Attributes\Test]
    public function executeFailsIfProviderCannotBeUsed(): void
    {
        $exception = new \Exception('Something went wrong.');

        $this->provider->shouldBeUsed = false;

        $this->commandTester->execute(['problem' => $exception->getMessage()]);

        self::assertSame(1, $this->commandTester->getStatusCode());
        self::assertStringContainsString(
            'The configured provider cannot be used to solve this problem.',
            $this->commandTester->getDisplay(),
        );
    }

    #[Framework\Attributes\Test]
    public function executeUsesJsonFormatterWithJsonOption(): void
    {
        $solution = Tests\Unit\DataProvider\SolutionDataProvider::get();

        $this->provider->solution = $solution;

        $this->commandTester->execute([
            'problem' => 'Something went wrong.',
            '--json' => true,
        ]);

        $output = $this->commandTester->getDisplay();

        self::assertJson($output);
        self::assertJsonStringEqualsJsonString(
            \json_encode($solution, JSON_THROW_ON_ERROR),
            $output,
        );
    }
}
