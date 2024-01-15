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
use Symfony\Component\Console;

use function sprintf;

/**
 * CacheFlushCommand
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
final class CacheFlushCommand extends Console\Command\Command
{
    public function __construct(
        private readonly Cache\SolutionsCache $cache,
    ) {
        parent::__construct('solver:cache:flush');
    }

    protected function configure(): void
    {
        $this->addArgument(
            'identifier',
            Console\Input\InputArgument::OPTIONAL,
            'Single cache entry identifier to remove',
        );
    }

    protected function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output): int
    {
        $io = new Console\Style\SymfonyStyle($input, $output);

        $identifier = (string)$input->getArgument('identifier');

        if ($identifier !== '') {
            // Remove single cache entry
            $this->cache->remove($identifier);
            $io->success(
                sprintf('Cache entry with identifier "%s" successfully removed.', $identifier),
            );
        } else {
            // Flush all caches
            $this->cache->flush();
            $io->success('Solutions cache successfully cleared.');
        }

        return self::SUCCESS;
    }
}
