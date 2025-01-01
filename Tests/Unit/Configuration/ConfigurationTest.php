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

namespace EliasHaeussler\Typo3Solver\Tests\Unit\Configuration;

use EliasHaeussler\Typo3Solver as Src;
use EliasHaeussler\Typo3Solver\Tests;
use PHPUnit\Framework;
use TYPO3\TestingFramework;

/**
 * ConfigurationTest
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
final class ConfigurationTest extends TestingFramework\Core\Unit\UnitTestCase
{
    private Tests\Unit\Fixtures\DummyConfigurationProvider $configurationProvider;
    private Src\Configuration\Configuration $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->configurationProvider = new Tests\Unit\Fixtures\DummyConfigurationProvider();
        $this->subject = new Src\Configuration\Configuration($this->configurationProvider);
    }

    #[Framework\Attributes\Test]
    public function getApiKeyReturnsNullIfNoApiKeyIsConfigured(): void
    {
        self::assertNull($this->subject->getApiKey());
    }

    #[Framework\Attributes\Test]
    public function getApiKeyReturnsNullIfConfiguredApiKeyIsInvalid(): void
    {
        $this->configurationProvider->configuration = [
            'api/key' => '',
        ];

        self::assertNull($this->subject->getApiKey());
    }

    #[Framework\Attributes\Test]
    public function getApiKeyReturnsConfiguredApiKey(): void
    {
        $this->configurationProvider->configuration = [
            'api/key' => 'foo',
        ];

        self::assertSame('foo', $this->subject->getApiKey());
    }

    #[Framework\Attributes\Test]
    public function getModelReturnsDefaultModelIfNoModelIsConfigured(): void
    {
        self::assertSame('gpt-3.5-turbo-0301', $this->subject->getModel());
    }

    #[Framework\Attributes\Test]
    public function getModelReturnsDefaultModelIfConfiguredModelIsInvalid(): void
    {
        $this->configurationProvider->configuration = [
            'attributes/model' => '',
        ];

        self::assertSame('gpt-3.5-turbo-0301', $this->subject->getModel());
    }

    #[Framework\Attributes\Test]
    public function getModelReturnsConfiguredModel(): void
    {
        $this->configurationProvider->configuration = [
            'attributes/model' => 'foo',
        ];

        self::assertSame('foo', $this->subject->getModel());
    }

    #[Framework\Attributes\Test]
    public function getMaxTokensReturnsDefaultMaxTokensIfNoMaxTokensAreConfigured(): void
    {
        self::assertSame(300, $this->subject->getMaxTokens());
    }

    #[Framework\Attributes\Test]
    public function getMaxTokensReturnsDefaultMaxTokensIfConfiguredMaxTokensAreInvalid(): void
    {
        $this->configurationProvider->configuration = [
            'attributes/maxTokens' => 'foo',
        ];

        self::assertSame(300, $this->subject->getMaxTokens());
    }

    #[Framework\Attributes\Test]
    public function getMaxTokensReturnsConfiguredMaxTokens(): void
    {
        $this->configurationProvider->configuration = [
            'attributes/maxTokens' => 150,
        ];

        self::assertSame(150, $this->subject->getMaxTokens());
    }

    #[Framework\Attributes\Test]
    public function getTemperatureReturnsDefaultTemperatureIfNoTemperatureIsConfigured(): void
    {
        self::assertSame(0.5, $this->subject->getTemperature());
    }

    #[Framework\Attributes\Test]
    public function getTemperatureReturnsDefaultTemperatureIfConfiguredTemperatureIsInvalid(): void
    {
        $this->configurationProvider->configuration = [
            'attributes/temperature' => 200,
        ];

        self::assertSame(0.5, $this->subject->getTemperature());
    }

    #[Framework\Attributes\Test]
    public function getTemperatureReturnsConfiguredTemperature(): void
    {
        $this->configurationProvider->configuration = [
            'attributes/temperature' => 0.75,
        ];

        self::assertSame(0.75, $this->subject->getTemperature());
    }

    #[Framework\Attributes\Test]
    public function getNumberOfCompletionsReturnsDefaultNumberOfCompletionsIfNoNumberOfCompletionsIsConfigured(): void
    {
        self::assertSame(1, $this->subject->getNumberOfCompletions());
    }

    #[Framework\Attributes\Test]
    public function getNumberOfCompletionsReturnsDefaultNumberOfCompletionsIfConfiguredNumberOfCompletionsIsInvalid(): void
    {
        $this->configurationProvider->configuration = [
            'attributes/numberOfCompletions' => 'foo',
        ];

        self::assertSame(1, $this->subject->getNumberOfCompletions());
    }

    #[Framework\Attributes\Test]
    public function getNumberOfCompletionsReturnsConfiguredNumberOfCompletions(): void
    {
        $this->configurationProvider->configuration = [
            'attributes/numberOfCompletions' => 5,
        ];

        self::assertSame(5, $this->subject->getNumberOfCompletions());
    }

    #[Framework\Attributes\Test]
    public function getCacheLifetimeReturnsDefaultCacheLifetimeIfNoCacheLifetimeIsConfigured(): void
    {
        self::assertSame(60 * 60 * 24, $this->subject->getCacheLifetime());
    }

    #[Framework\Attributes\Test]
    public function getCacheLifetimeReturnsDefaultCacheLifetimeIfConfiguredCacheLifetimeIsInvalid(): void
    {
        $this->configurationProvider->configuration = [
            'cache/lifetime' => 'foo',
        ];

        self::assertSame(60 * 60 * 24, $this->subject->getCacheLifetime());
    }

    #[Framework\Attributes\Test]
    public function getCacheLifetimeReturnsConfiguredCacheLifetime(): void
    {
        $this->configurationProvider->configuration = [
            'cache/lifetime' => 3600,
        ];

        self::assertSame(3600, $this->subject->getCacheLifetime());
    }

    #[Framework\Attributes\Test]
    public function isCacheEnabledReturnsTrueIfCacheLifetimeIsGreaterThanZero(): void
    {
        $this->configurationProvider->configuration = [
            'cache/lifetime' => 3600,
        ];

        self::assertTrue($this->subject->isCacheEnabled());
    }

    #[Framework\Attributes\Test]
    public function isCacheEnabledReturnsFalseIfCacheLifetimeIsZero(): void
    {
        $this->configurationProvider->configuration = [
            'cache/lifetime' => 0,
        ];

        self::assertFalse($this->subject->isCacheEnabled());
    }

    #[Framework\Attributes\Test]
    public function getProviderReturnsDefaultProviderIfNoProviderIsConfigured(): void
    {
        self::assertInstanceOf(
            Src\ProblemSolving\Solution\Provider\OpenAISolutionProvider::class,
            $this->subject->getProvider(),
        );
    }

    #[Framework\Attributes\Test]
    public function getProviderReturnsDefaultProviderIfConfiguredProviderIsInvalid(): void
    {
        $this->configurationProvider->configuration = [
            'provider' => 'foo',
        ];

        self::assertInstanceOf(
            Src\ProblemSolving\Solution\Provider\OpenAISolutionProvider::class,
            $this->subject->getProvider(),
        );
    }

    #[Framework\Attributes\Test]
    public function getProviderReturnsConfiguredProvider(): void
    {
        $this->configurationProvider->configuration = [
            'provider' => Tests\Unit\Fixtures\DummySolutionProvider::class,
        ];

        self::assertInstanceOf(
            Tests\Unit\Fixtures\DummySolutionProvider::class,
            $this->subject->getProvider(),
        );
    }

    #[Framework\Attributes\Test]
    public function getPromptReturnsDefaultPromptIfNoPromptIsConfigured(): void
    {
        self::assertInstanceOf(
            Src\ProblemSolving\Solution\Prompt\DefaultPrompt::class,
            $this->subject->getPrompt(),
        );
    }

    #[Framework\Attributes\Test]
    public function getPromptReturnsDefaultPromptIfConfiguredPromptIsInvalid(): void
    {
        $this->configurationProvider->configuration = [
            'prompt' => 'foo',
        ];

        self::assertInstanceOf(
            Src\ProblemSolving\Solution\Prompt\DefaultPrompt::class,
            $this->subject->getPrompt(),
        );
    }

    #[Framework\Attributes\Test]
    public function getPromptReturnsConfiguredPrompt(): void
    {
        $this->configurationProvider->configuration = [
            'prompt' => Tests\Unit\Fixtures\DummyPrompt::class,
        ];

        self::assertInstanceOf(
            Tests\Unit\Fixtures\DummyPrompt::class,
            $this->subject->getPrompt(),
        );
    }

    #[Framework\Attributes\Test]
    public function getIgnoredCodesReturnsEmptyArrayIfCodesToIgnoreAreNotConfigured(): void
    {
        self::assertSame([], $this->subject->getIgnoredCodes());
    }

    #[Framework\Attributes\Test]
    public function getIgnoredCodesReturnsEmptyArrayIfCodesToIgnoreAreInvalid(): void
    {
        $this->configurationProvider->configuration = [
            'ignoredCodes' => 123,
        ];

        self::assertSame([], $this->subject->getIgnoredCodes());
    }

    #[Framework\Attributes\Test]
    public function getIgnoredCodesReturnsConfiguredCodesToIgnore(): void
    {
        $this->configurationProvider->configuration = [
            'ignoredCodes' => '1675962685, foo, 123',
        ];

        self::assertSame([1675962685, 123], $this->subject->getIgnoredCodes());
    }
}
