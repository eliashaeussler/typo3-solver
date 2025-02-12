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

namespace EliasHaeussler\Typo3Solver\Command;

use EliasHaeussler\Typo3Solver\Configuration;
use EliasHaeussler\Typo3Solver\ProblemSolving;
use Symfony\Component\Console;

/**
 * ListModelsCommand
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
final class ListModelsCommand extends Console\Command\Command
{
    private readonly ProblemSolving\Solution\Provider\SolutionProvider $solutionProvider;

    public function __construct(
        private readonly Configuration\Configuration $configuration,
        ProblemSolving\Solution\Provider\SolutionProvider $solutionProvider = null,
    ) {
        parent::__construct('solver:list-models');

        $this->solutionProvider = $solutionProvider ?? $this->configuration->getProvider();
    }

    protected function configure(): void
    {
        $this->addOption(
            'all',
            'a',
            Console\Input\InputOption::VALUE_NONE,
            'List all available models, even those which cannot be used',
        );
    }

    protected function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output): int
    {
        $io = new Console\Style\SymfonyStyle($input, $output);
        $listAll = $input->getOption('all');

        // Retrieve models via solution provider
        $modelListResponse = $this->solutionProvider->listModels($listAll);

        if ($listAll) {
            $io->title('Available AI models');
        } else {
            $io->title('Supported AI models');
        }

        // Map responses to model IDs
        $models = \array_map(
            $this->decorateModel(...),
            $modelListResponse,
        );

        \sort($models);

        $io->listing($models);

        if ($listAll) {
            $io->writeln('💡 <comment>Only a limited set of models can be used with this extension.</comment>');
            $io->newLine();
        }

        return self::SUCCESS;
    }

    private function decorateModel(ProblemSolving\Solution\Provider\Model\AiModel $model): string
    {
        if ($model->createdAt === null) {
            return $model->name;
        }

        return \sprintf('%s <fg=gray>(created at %s)</>', $model->name, $model->createdAt->format('d/m/Y'));
    }
}
