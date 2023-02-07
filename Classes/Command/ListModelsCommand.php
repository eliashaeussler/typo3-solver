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

use EliasHaeussler\Typo3Solver\Configuration;
use OpenAI;
use Symfony\Component\Console;

use function array_map;
use function sort;

/**
 * ListModelsCommand
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
final class ListModelsCommand extends Console\Command\Command
{
    protected static $defaultName = 'solver:list-models';

    private readonly OpenAI\Client $client;

    public function __construct()
    {
        parent::__construct();
        $this->client = OpenAI::client((new Configuration\Configuration())->getApiKey() ?? '');
    }

    protected function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output): int
    {
        $io = new Console\Style\SymfonyStyle($input, $output);

        $models = array_map(
            static fn (OpenAI\Responses\Models\RetrieveResponse $response): string => $response->id,
            $this->client->models()->list()->data,
        );

        sort($models);

        $io->listing($models);

        return self::SUCCESS;
    }
}
