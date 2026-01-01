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

namespace EliasHaeussler\Typo3Solver\Tests\Unit\Cache\Serializer;

use EliasHaeussler\Typo3Solver as Src;
use PHPUnit\Framework;
use TYPO3\TestingFramework;

/**
 * ExceptionSerializerTest
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
#[Framework\Attributes\CoversClass(Src\Cache\Serializer\ExceptionSerializer::class)]
final class ExceptionSerializerTest extends TestingFramework\Core\Unit\UnitTestCase
{
    private Src\Cache\Serializer\ExceptionSerializer $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new Src\Cache\Serializer\ExceptionSerializer();
    }

    #[Framework\Attributes\Test]
    public function serializeReturnsSerializedException(): void
    {
        $exception = Src\Exception\CustomSolvableException::create(
            'Something went wrong.',
            123,
            __FILE__,
            1,
        );

        $expected = [
            'className' => Src\Exception\CustomSolvableException::class,
            'exception' => [
                'message' => 'Something went wrong.',
                'code' => 123,
                'file' => __FILE__,
                'line' => 1,
            ],
        ];

        self::assertSame($expected, $this->subject->serialize($exception));
    }

    #[Framework\Attributes\Test]
    public function deserializeReturnsDeserializedException(): void
    {
        $exceptionArray = [
            'className' => Src\Exception\CustomSolvableException::class,
            'exception' => [
                'message' => 'Something went wrong.',
                'code' => 123,
                'file' => __FILE__,
                'line' => 1,
            ],
        ];

        $actual = $this->subject->deserialize($exceptionArray);

        self::assertInstanceOf(Src\Exception\CustomSolvableException::class, $actual);
        self::assertSame('Something went wrong.', $actual->getMessage());
        self::assertSame(123, $actual->getCode());
        self::assertSame(__FILE__, $actual->getFile());
        self::assertSame(1, $actual->getLine());
    }
}
