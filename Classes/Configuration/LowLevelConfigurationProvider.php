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

use EliasHaeussler\Typo3Solver\ProblemSolving;
use Exception;
use TYPO3\CMS\Core;

/**
 * LowLevelConfigurationProvider
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
final class LowLevelConfigurationProvider implements ConfigurationProvider
{
    /**
     * @var array{
     *     api?: array{key?: string},
     *     attributes?: array{model?: string, maxTokens?: int, temperature?: float},
     *     cache?: array{lifetime?: int},
     *     provider?: class-string<ProblemSolving\Solution\Provider\SolutionProvider>,
     *     prompt?: class-string<ProblemSolving\Solution\Prompt\Prompt>
     * }
     */
    private readonly array $configuration;

    public function __construct()
    {
        /* @phpstan-ignore-next-line */
        $this->configuration = $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['solver'] ?? [];
    }

    public function get(string $configPath, mixed $default = null): mixed
    {
        try {
            return Core\Utility\ArrayUtility::getValueByPath($this->configuration, $configPath) ?? $default;
        } catch (Exception) {
            return $default;
        }
    }
}
