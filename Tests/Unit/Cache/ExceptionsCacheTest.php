<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS extension "solver".
 *
 * Copyright (C) 2023-2024 Elias Häußler <elias@haeussler.dev>
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

namespace EliasHaeussler\Typo3Solver\Tests\Unit\Cache;

use EliasHaeussler\Typo3Solver as Src;
use PHPUnit\Framework;
use Symfony\Component\Filesystem;
use TYPO3\TestingFramework;

/**
 * ExceptionsCacheTest
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
final class ExceptionsCacheTest extends TestingFramework\Core\Unit\UnitTestCase
{
    private Src\Cache\ExceptionsCache $subject;
    private \Exception $exception;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new Src\Cache\ExceptionsCache();
        $this->exception = new \Exception('Something went wrong.', 123);
    }

    #[Framework\Attributes\Test]
    public function constructorCreatesCacheFileIfNotExists(): void
    {
        $cacheFile = \dirname(__DIR__, 3) . '/var/cache/data/tx_solver/exceptions.php';
        $filesystem = new Filesystem\Filesystem();

        $filesystem->remove($cacheFile);

        self::assertFileDoesNotExist($cacheFile);

        new Src\Cache\ExceptionsCache();

        self::assertFileExists($cacheFile);
        self::assertSame([], require $cacheFile);
    }

    #[Framework\Attributes\Test]
    public function getReturnsNullOnEmptyCache(): void
    {
        $this->subject->flush();

        self::assertNull($this->subject->get('foo'));
    }

    #[Framework\Attributes\Test]
    public function getReturnsNullOnMissingCacheEntry(): void
    {
        $this->subject->flush();
        $this->subject->set($this->exception);

        self::assertNotNull($this->subject->get($this->subject->getIdentifier($this->exception)));
        self::assertNull($this->subject->get('foo'));
    }

    #[Framework\Attributes\Test]
    public function getReturnsCacheEntry(): void
    {
        $this->subject->flush();
        $this->subject->set($this->exception);

        $actual = $this->subject->get($this->subject->getIdentifier($this->exception));

        self::assertInstanceOf($this->exception::class, $actual);
        self::assertSame($this->exception->getMessage(), $actual->getMessage());
        self::assertSame($this->exception->getCode(), $actual->getCode());
        self::assertSame($this->exception->getFile(), $actual->getFile());
        self::assertSame($this->exception->getLine(), $actual->getLine());
    }

    #[Framework\Attributes\Test]
    public function removeRemovesGivenExceptionFromCache(): void
    {
        $this->subject->set($this->exception);

        self::assertNotNull($this->subject->get($this->subject->getIdentifier($this->exception)));

        $this->subject->remove($this->exception);

        self::assertNull($this->subject->get($this->subject->getIdentifier($this->exception)));
    }
}
