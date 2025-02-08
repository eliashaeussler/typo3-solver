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

namespace EliasHaeussler\Typo3Solver\Tests\Unit\Http;

use EliasHaeussler\Typo3Solver as Src;
use PHPUnit\Framework;
use TYPO3\TestingFramework;

/**
 * ClientFactoryTest
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
#[Framework\Attributes\CoversClass(Src\Http\ClientFactory::class)]
final class ClientFactoryTest extends TestingFramework\Core\Unit\UnitTestCase
{
    private Src\Tests\Unit\Fixtures\DummyConfigurationProvider $provider;
    private Src\Http\ClientFactory $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->provider = new Src\Tests\Unit\Fixtures\DummyConfigurationProvider();
        $this->subject = new Src\Http\ClientFactory(new Src\Configuration\Configuration($this->provider));
    }

    #[Framework\Attributes\Test]
    public function getThrowsExceptionIfApiKeyIsNotConfigured(): void
    {
        $this->expectExceptionObject(
            Src\Exception\ApiKeyMissingException::create(),
        );

        $this->subject->get();
    }

    #[Framework\Attributes\Test]
    public function getReturnsOpenAIClient(): void
    {
        $exception = null;

        $this->provider->configuration = [
            'api/key' => 'foo',
        ];

        try {
            $this->subject->get();
        } catch (Src\Exception\ApiKeyMissingException $exception) {
            // Intended fallthrough.
        }

        self::assertNull($exception);
    }
}
