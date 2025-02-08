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

use OpenAI\Client;
use OpenAI\Responses;
use Symfony\Component\Console;

/**
 * ListModelsCommand
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
final class ListModelsCommand extends Console\Command\Command
{
    public function __construct(
        private readonly Client $client,
    ) {
        parent::__construct('solver:list-models');
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

        // Retrieve all available models
        $modelListResponse = $this->client->models()->list()->data;

        if ($listAll) {
            $io->title('Available OpenAI models');
        } else {
            $io->title('Available GPT models');

            // Filter by GPT models
            $modelListResponse = \array_filter($modelListResponse, $this->isSupportedModel(...));
        }

        // Map responses to model IDs
        $models = \array_map(
            $this->decorateModel(...),
            $modelListResponse,
        );

        \sort($models);

        $io->listing($models);

        if ($listAll) {
            $io->writeln('💡 <comment>Only GPT models can be used with this extension.</comment>');
            $io->newLine();
        }

        return self::SUCCESS;
    }

    /**
     * @see https://platform.openai.com/docs/models#model-endpoint-compatibility
     */
    private function isSupportedModel(Responses\Models\RetrieveResponse $response): bool
    {
        $identifier = \strtolower($response->id);

        if (!\str_starts_with($identifier, 'gpt-') && !\str_starts_with($identifier, 'chatgpt-')) {
            return false;
        }

        return !\str_contains($identifier, '-realtime') && !\str_contains($identifier, '-audio');
    }

    private function decorateModel(Responses\Models\RetrieveResponse $response): string
    {
        return \sprintf('%s <fg=gray>(created at %s)</>', $response->id, \date('d/m/Y', $response->created));
    }
}
