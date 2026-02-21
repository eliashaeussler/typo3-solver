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

use EliasHaeussler\PhpCsFixerConfig;
use TYPO3\CodingStandards;

$header = PhpCsFixerConfig\Rules\Header::create(
    'solver',
    PhpCsFixerConfig\Package\Type::TYPO3Extension,
    PhpCsFixerConfig\Package\Author::create('Elias Häußler', 'elias@haeussler.dev'),
    PhpCsFixerConfig\Package\CopyrightRange::from(2023),
    PhpCsFixerConfig\Package\License::GPL2OrLater,
);

$config = CodingStandards\CsFixerConfig::create();
$finder = $config->getFinder()
    ->in(dirname(__DIR__, 2))
    ->ignoreVCSIgnored(true)
    ->ignoreDotFiles(false)
;

return PhpCsFixerConfig\Config::create()
    ->withConfig($config)
    ->withRule($header)
;
