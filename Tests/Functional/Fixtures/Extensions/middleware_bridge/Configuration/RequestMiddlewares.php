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

return (static function () {
    /** @var array<string, array<string, array<string, mixed>>> $middlewares */
    $middlewares = require dirname(__DIR__, 6) . '/Configuration/RequestMiddlewares.php';

    foreach ($middlewares as &$contextMiddlewares) {
        foreach ($contextMiddlewares as &$middleware) {
            if (!is_array($middleware['after'] ?? null)) {
                $middleware['after'] = [];
            }

            // Move all middlewares behind TF related middlewares
            $middleware['after'][] = 'typo3/json-response/encoder';
        }
    }

    return $middlewares;
})();
