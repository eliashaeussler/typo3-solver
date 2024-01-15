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

namespace EliasHaeussler\Typo3Solver\Configuration;

use EliasHaeussler\Typo3Solver\Extension;
use Exception;
use TYPO3\CMS\Core;

use function is_array;

/**
 * LowLevelConfigurationProvider
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
final class LowLevelConfigurationProvider implements ConfigurationProvider
{
    public function get(string $configPath, mixed $default = null): mixed
    {
        /* @phpstan-ignore-next-line */
        $extensionConfiguration = $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS'][Extension::KEY] ?? null;

        if (!is_array($extensionConfiguration)) {
            return $default;
        }

        try {
            return Core\Utility\ArrayUtility::getValueByPath($extensionConfiguration, $configPath) ?? $default;
        } catch (Exception) {
            return $default;
        }
    }
}
