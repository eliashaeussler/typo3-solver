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
    protected static $defaultName = 'solver:solve';

    private readonly ProblemSolving\Solution\Provider\SolutionProvider $solutionProvider;
    private readonly Formatter\CliFormatter $cliFormatter;
    private readonly Formatter\JsonFormatter $jsonFormatter;

    public function __construct(
        private readonly Configuration\Configuration $configuration,
        private readonly Cache\SolutionsCache $cache,
        ProblemSolving\Solution\Provider\SolutionProvider $solutionProvider = null,
    ) {
        parent::__construct();

        $this->solutionProvider = $solutionProvider ?? $this->configuration->getProvider();
        $this->cliFormatter = new Formatter\CliFormatter();
        $this->jsonFormatter = new Formatter\JsonFormatter();
    }

    protected function configure(): void
    {
        $this->addArgument(
            'problem',
            Console\Input\InputArgument::REQUIRED,
            'The exception message to solve',
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

    protected function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output): int
    {
        $io = new Console\Style\SymfonyStyle($input, $output);

        // Parse parameters
        $problem = $input->getArgument('problem');
        $code = (int)$input->getOption('code');
        $file = $input->getOption('file');
        $line = (int)$input->getOption('line');
        $refresh = $input->getOption('refresh');
        $json = $input->getOption('json');

        // Create exception
        $exception = Exception\CustomSolvableException::create($problem, $code, $file, $line);

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
        $solver = new ProblemSolving\Solver($this->solutionProvider, $formatter);

        return $solver->solve($exception);
    }

    private function removeCacheEntry(Throwable $exception): void
    {
        $problem = new ProblemSolving\Problem\Problem(
            $exception,
            $this->solutionProvider,
            $this->configuration->getPrompt()->generate($exception),
        );

        $this->cache->remove($problem);
    }

    private function createFormatter(Console\Output\OutputInterface $output, bool $json): Formatter\Formatter
    {
        if ($json) {
            return $this->jsonFormatter;
        }

        return $this->cliFormatter->setOutput($output);
    }
}
