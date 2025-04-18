<?php

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

use EliasHaeussler\Typo3Solver as Src;
use EliasHaeussler\Typo3Solver\Tests;
use Symfony\Component\Console;
use TYPO3\CMS\Core;

// Create core environment
Core\Core\SystemEnvironmentBuilder::run(0, Core\Core\SystemEnvironmentBuilder::REQUESTTYPE_CLI);

// Initialize default TYPO3_CONF_VARS
$configurationManager = new Core\Configuration\ConfigurationManager();
$GLOBALS['TYPO3_CONF_VARS'] = $configurationManager->getDefaultConfiguration();

// Create application
$application = new Console\Application();

// Add console commands
$application->add(new Src\Command\CacheFlushCommand(new Src\Cache\SolutionsCache()));
$application->add(
    new Src\Command\ListModelsCommand(
        new Src\Configuration\Configuration(),
        Tests\Unit\Fixtures\DummySolutionProvider::create(),
    ),
);
$application->add(
    new Src\Command\SolveCommand(
        new Src\Configuration\Configuration(),
        new Src\Cache\ExceptionsCache(),
        new Src\Cache\SolutionsCache(),
        new Src\Formatter\CliFormatter(new Src\View\TemplateRenderer()),
        new Src\Formatter\JsonFormatter(),
        Tests\Unit\Fixtures\DummySolutionProvider::create(),
    ),
);

return $application;
