<?php

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

use EliasHaeussler\Typo3Solver\Cache;
use EliasHaeussler\Typo3Solver\Command;
use EliasHaeussler\Typo3Solver\Configuration;
use Symfony\Component\Console;

$application = new Console\Application();
$application->add(new Command\CacheFlushCommand(new Cache\SolutionsCache()));
$application->add(new Command\ListModelsCommand());
$application->add(new Command\SolveCommand(new Configuration\Configuration(), new Cache\SolutionsCache()));

return $application;
