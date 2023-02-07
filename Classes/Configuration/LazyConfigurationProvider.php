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

/**
 * LazyConfigurationProvider
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
final class LazyConfigurationProvider implements ConfigurationProvider
{
    private static ?ConfigurationProvider $provider = null;

    public function get(string $configPath, mixed $default = null): mixed
    {
        return $this->getProvider()->get($configPath, $default);
    }

    private function getProvider(): ConfigurationProvider
    {
        if (self::$provider === null) {
            self::$provider = ExtensionConfigurationProvider::canBeUsed()
                ? new ExtensionConfigurationProvider()
                : new LowLevelConfigurationProvider()
            ;
        }

        return self::$provider;
    }
}
