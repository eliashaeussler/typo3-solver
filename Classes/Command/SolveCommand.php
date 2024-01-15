<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS extension "solver".
 *
 * Copyright (C) 2024 Elias Häußler <elias@haeussler.dev>
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

namespace EliasHaeussler\Typo3Solver\Command;

use EliasHaeussler\Typo3Solver\Cache;
use EliasHaeussler\Typo3Solver\Configuration;
use EliasHaeussler\Typo3Solver\Exception;
use EliasHaeussler\Typo3Solver\Formatter;
use EliasHaeussler\Typo3Solver\ProblemSolving;
use Symfony\Component\Console;
use Throwable;

/**
 * SolveCommand
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
final class SolveCommand extends Console\Command\Command
{
    private readonly ProblemSolving\Solution\Provider\SolutionProvider $solutionProvider;

    public function __construct(
        private readonly Configuration\Configuration $configuration,
        private readonly Cache\ExceptionsCache $exceptionsCache,
        private readonly Cache\SolutionsCache $solutionsCache,
        private readonly Formatter\CliFormatter $cliFormatter,
        private readonly Formatter\JsonFormatter $jsonFormatter,
        ProblemSolving\Solution\Provider\SolutionProvider $solutionProvider = null,
    ) {
        parent::__construct('solver:solve');

        $this->solutionProvider = $solutionProvider ?? $this->configuration->getProvider();
    }

    protected function configure(): void
    {
        $this->addArgument(
            'problem',
            Console\Input\InputArgument::OPTIONAL,
            'The exception message to solve',
        );

        $this->addOption(
            'identifier',
            'i',
            Console\Input\InputOption::VALUE_REQUIRED,
            'The exception cache identifier',
        );
        $this->addOption(
            'code',
            'c',
            Console\Input\InputOption::VALUE_REQUIRED,
            'Optional exception code',
            0,
        );
        $this->addOption(
            'file',
            'f',
            Console\Input\InputOption::VALUE_REQUIRED,
            'Optional file name where the problem occurs',
            '',
        );
        $this->addOption(
            'line',
            'l',
            Console\Input\InputOption::VALUE_REQUIRED,
            'Optional line number in the given file',
            0,
        );
        $this->addOption(
            'refresh',
            'r',
            Console\Input\InputOption::VALUE_NONE,
            'Whether to refresh a cached solution',
        );
        $this->addOption(
            'json',
            'j',
            Console\Input\InputOption::VALUE_NONE,
            'Provide output as JSON',
        );
    }

    /**
     * @throws Exception\IOException
     * @throws Exception\MissingCacheEntryException
     */
    protected function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output): int
    {
        $io = new Console\Style\SymfonyStyle($input, $output);

        // Parse parameters
        $problem = $input->getArgument('problem');
        $identifier = $input->getOption('identifier');
        $code = (int)$input->getOption('code');
        $file = $input->getOption('file');
        $line = (int)$input->getOption('line');
        $refresh = $input->getOption('refresh');
        $json = $input->getOption('json');

        // Validate parameters
        if ($problem !== null && $identifier !== null) {
            throw Exception\IOException::forConflictingParameters('problem', '--identifier');
        }

        // Create exception
        if ($identifier !== null) {
            $exception = $this->getExceptionFromCache($identifier);
        } elseif ($problem !== null) {
            $exception = Exception\CustomSolvableException::create($problem, $code, $file, $line);
        } else {
            throw Exception\IOException::forMissingRequiredParameter('problem');
        }

        // Remove cache entry when requested
        if ($refresh) {
            $this->removeCacheEntry($exception);
        }

        // Solve problem
        $solution = $this->solveProblem($exception, $this->createFormatter($output, $json));

        // Early return if problem cannot be solved
        if ($solution === null) {
            $io->error('The configured provider cannot be used to solve this problem.');

            return self::FAILURE;
        }

        // Print solution
        $output->writeln($solution);

        return self::SUCCESS;
    }

    private function solveProblem(Throwable $exception, Formatter\Formatter $formatter): ?string
    {
        $solver = new ProblemSolving\Solver($this->configuration, $formatter, $this->solutionProvider);

        return $solver->solve($exception);
    }

    /**
     * @throws Exception\MissingCacheEntryException
     */
    private function getExceptionFromCache(string $identifier): Throwable
    {
        $exception = $this->exceptionsCache->get($identifier);

        if ($exception === null) {
            throw Exception\MissingCacheEntryException::create($identifier);
        }

        return $exception;
    }

    private function removeCacheEntry(Throwable $exception): void
    {
        $problem = new ProblemSolving\Problem\Problem(
            $exception,
            $this->solutionProvider,
            $this->configuration->getPrompt()->generate($exception),
        );

        $this->solutionsCache->remove($problem);
    }

    private function createFormatter(Console\Output\OutputInterface $output, bool $json): Formatter\Formatter
    {
        if ($json) {
            return $this->jsonFormatter;
        }

        return $this->cliFormatter->setOutput($output);
    }
}
