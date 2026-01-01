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

namespace EliasHaeussler\Typo3Solver\Tests\Unit\ProblemSolving\Solution\Model;

use EliasHaeussler\Typo3Solver as Src;
use PHPUnit\Framework;
use TYPO3\TestingFramework;

/**
 * MessageTest
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
#[Framework\Attributes\CoversClass(Src\ProblemSolving\Solution\Model\Message::class)]
final class MessageTest extends TestingFramework\Core\Unit\UnitTestCase
{
    private Src\ProblemSolving\Solution\Model\Message $subject;

    public function setUp(): void
    {
        parent::setUp();

        $this->subject = new Src\ProblemSolving\Solution\Model\Message('role', 'foo');
    }

    #[Framework\Attributes\Test]
    public function toArrayReturnsArrayRepresentation(): void
    {
        $expected = [
            'role' => 'role',
            'content' => 'foo',
        ];

        self::assertSame($expected, $this->subject->toArray());
    }

    #[Framework\Attributes\Test]
    public function subjectIsJsonSerializable(): void
    {
        $expected = [
            'role' => 'role',
            'content' => 'foo',
        ];

        self::assertJsonStringEqualsJsonString(
            \json_encode($expected, JSON_THROW_ON_ERROR),
            \json_encode($this->subject, JSON_THROW_ON_ERROR),
        );
    }
}
