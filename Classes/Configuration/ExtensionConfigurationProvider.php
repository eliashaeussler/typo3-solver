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

namespace EliasHaeussler\Typo3Solver\Configuration;

use Exception;
use TYPO3\CMS\Core;

/**
 * ExtensionConfigurationProvider
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
final class ExtensionConfigurationProvider implements ConfigurationProvider
{
    private readonly Core\Configuration\ExtensionConfiguration $configuration;

    public function __construct()
    {
        $this->configuration = Core\Utility\GeneralUtility::makeInstance(Core\Configuration\ExtensionConfiguration::class);
    }

    public function get(string $configPath, mixed $default = null): mixed
    {
        try {
            return $this->configuration->get('solver', $configPath);
        } catch (Core\Exception) {
            return $default;
        }
    }

    public static function canBeUsed(): bool
    {
        // Check if a global container is available.
        // If yes, extension configuration should be
        // available as well. If no, this provider
        // cannot be used.

        try {
            Core\Utility\GeneralUtility::getContainer();
        } catch (Exception) {
            return false;
        }

        return true;
    }
}
