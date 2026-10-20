<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS extension "solver".
 *
 * Copyright (C) 2023-2026 Elias Häußler <elias@haeussler.dev>
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

use Rector\Config\RectorConfig;
use Ssch\TYPO3Rector\Set\Typo3LevelSetList;

$rootPath = dirname(__DIR__, 2);

return RectorConfig::configure()
    ->withPaths([
        $rootPath . '/Classes',
        $rootPath . '/Configuration',
        $rootPath . '/Tests',
    ])
    ->withPhpSets(php82: true)
    ->withComposerBased(phpunit: true, symfony: true)
    ->withSets([
        Typo3LevelSetList::UP_TO_TYPO3_13,
    ])
;
