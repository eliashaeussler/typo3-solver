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

namespace EliasHaeussler\Typo3Solver\Configuration;

use EliasHaeussler\Typo3Solver\ProblemSolving;
use TYPO3\CMS\Core;

/**
 * Configuration.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
final readonly class Configuration
{
    private const DEFAULT_MODEL = 'gpt-4o-mini';
    private const DEFAULT_TOKENS = 300;
    private const DEFAULT_TEMPERATURE = 0.5;
    private const DEFAULT_NUMBER_OF_COMPLETIONS = 1;
    private const DEFAULT_CACHE_LIFETIME = 60 * 60 * 24; // 1 day
    private const DEFAULT_PROVIDER = ProblemSolving\Solution\Provider\OpenAISolutionProvider::class;
    private const DEFAULT_PROMPT = ProblemSolving\Solution\Prompt\DefaultPrompt::class;

    public function __construct(
        private ConfigurationProvider $provider = new LazyConfigurationProvider(),
    ) {}

    public function getApiKey(): ?string
    {
        $apiKey = $this->provider->get('api/key');

        if (!\is_string($apiKey) || \trim($apiKey) === '') {
            return null;
        }

        return $apiKey;
    }

    public function getModel(): string
    {
        $model = $this->provider->get('attributes/model', self::DEFAULT_MODEL);

        if (!\is_string($model) || \trim($model) === '') {
            $model = self::DEFAULT_MODEL;
        }

        return $model;
    }

    public function getMaxTokens(): int
    {
        $maxTokens = $this->provider->get('attributes/maxTokens', self::DEFAULT_TOKENS);

        if (!\is_numeric($maxTokens) || $maxTokens <= 0) {
            $maxTokens = self::DEFAULT_TOKENS;
        }

        return (int)$maxTokens;
    }

    public function getTemperature(): float
    {
        $temperature = $this->provider->get('attributes/temperature', self::DEFAULT_TEMPERATURE);

        if (!\is_numeric($temperature) || $temperature < 0 || $temperature > 1) {
            $temperature = self::DEFAULT_TEMPERATURE;
        }

        return (float)$temperature;
    }

    public function getNumberOfCompletions(): int
    {
        $numberOfCompletions = $this->provider->get('attributes/numberOfCompletions', self::DEFAULT_NUMBER_OF_COMPLETIONS);

        if (!\is_numeric($numberOfCompletions) || $numberOfCompletions < 1) {
            $numberOfCompletions = self::DEFAULT_NUMBER_OF_COMPLETIONS;
        }

        return (int)$numberOfCompletions;
    }

    public function getCacheLifetime(): int
    {
        $cacheLifetime = $this->provider->get('cache/lifetime', self::DEFAULT_CACHE_LIFETIME);

        if (!\is_numeric($cacheLifetime) || $cacheLifetime < 0) {
            $cacheLifetime = self::DEFAULT_CACHE_LIFETIME;
        }

        return (int)$cacheLifetime;
    }

    public function isCacheEnabled(): bool
    {
        return $this->getCacheLifetime() > 0;
    }

    public function getProvider(): ProblemSolving\Solution\Provider\SolutionProvider
    {
        $providerClass = $this->provider->get('provider', self::DEFAULT_PROVIDER);

        if (!\is_string($providerClass)
            || !\class_exists($providerClass)
            || !\is_a($providerClass, ProblemSolving\Solution\Provider\SolutionProvider::class, true)
        ) {
            $providerClass = self::DEFAULT_PROVIDER;
        }

        return $providerClass::create();
    }

    public function getPrompt(): ProblemSolving\Solution\Prompt\Prompt
    {
        $promptClass = $this->provider->get('prompt', self::DEFAULT_PROMPT);

        if (!\is_string($promptClass)
            || !\class_exists($promptClass)
            || !\is_a($promptClass, ProblemSolving\Solution\Prompt\Prompt::class, true)
        ) {
            $promptClass = self::DEFAULT_PROMPT;
        }

        return $promptClass::create();
    }

    /**
     * @return list<int>
     */
    public function getIgnoredCodes(): array
    {
        $ignoredCodes = $this->provider->get('ignoredCodes', '');

        if (!\is_string($ignoredCodes)) {
            return [];
        }

        return \array_values(
            \array_map(
                intval(...),
                \array_filter(
                    Core\Utility\GeneralUtility::trimExplode(',', $ignoredCodes, true),
                    is_numeric(...),
                ),
            ),
        );
    }
}
