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

/** @noinspection PhpUndefinedVariableInspection */
$EM_CONF[$_EXTKEY] = [
    'title' => 'Solver',
    'description' => 'Extends TYPO3\'s exception handling with AI generated solutions. Problems can also be solved from command line. All OpenAI parameters are configurable and prompts and solution providers can be customized as desired.',
    'category' => 'misc',
    'version' => '0.1.1',
    'state' => 'beta',
    'clearCacheOnLoad' => true,
    'author' => 'Elias Häußler',
    'author_email' => 'elias@haeussler.dev',
    'constraints' => [
        'depends' => [
            'typo3' => '11.5.0-12.4.99',
        ],
    ],
];
