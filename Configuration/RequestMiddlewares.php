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

return [
    'frontend' => [
        'eliashaeussler/typo3-solver/ping' => [
            'target' => \EliasHaeussler\Typo3Solver\Middleware\PingMiddleware::class,
            'before' => [
                'typo3/cms-frontend/timetracker',
            ],
        ],
        'eliashaeussler/typo3-solver/solution' => [
            'target' => \EliasHaeussler\Typo3Solver\Middleware\SolutionMiddleware::class,
            'before' => [
                'typo3/cms-frontend/timetracker',
            ],
        ],
    ],
];
