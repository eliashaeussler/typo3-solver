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

namespace EliasHaeussler\Typo3Solver\Tests\Unit\Authentication;

use EliasHaeussler\Typo3Solver as Src;
use Symfony\Component\Filesystem;
use TYPO3\TestingFramework;

use function file;
use function file_get_contents;

/**
 * StreamAuthenticationTest
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
final class StreamAuthenticationTest extends TestingFramework\Core\Unit\UnitTestCase
{
    private Filesystem\Filesystem $filesystem;
    private string $filename;
    private Src\Authentication\StreamAuthentication $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->filename = dirname(__DIR__, 3) . '/var/transient/tx_solver/stream_auth.txt';
        $this->filesystem = new Filesystem\Filesystem();
        $this->filesystem->remove($this->filename);
        $this->subject = new Src\Authentication\StreamAuthentication();
    }

    /**
     * @test
     */
    public function constructorCreatesTransientFileIfNotExists(): void
    {
        $this->filesystem->remove($this->filename);

        self::assertFileDoesNotExist($this->filename);

        new Src\Authentication\StreamAuthentication();

        self::assertFileExists($this->filename);
        self::assertSame('', file_get_contents($this->filename));
    }

    /**
     * @test
     */
    public function authenticateThrowsExceptionIfFileCannotBeRead(): void
    {
        $this->filesystem->remove($this->filename);

        $this->expectExceptionObject(
            Src\Exception\AuthenticationFailureException::create(),
        );

        $this->subject->authenticate('foo');
    }

    /**
     * @test
     */
    public function authenticateThrowsExceptionIfHashIsNotRegistered(): void
    {
        $this->expectExceptionObject(
            Src\Exception\AuthenticationFailureException::create(),
        );

        $this->subject->authenticate('foo');
    }

    /**
     * @test
     */
    public function authenticateRemovesHashFromTransientFileOnSuccessfulAuthentication(): void
    {
        $registeredHash = $this->subject->register();
        $registeredHashesBeforeAuthentication = file($this->filename);

        self::assertIsArray($registeredHashesBeforeAuthentication);
        self::assertContains($registeredHash, $registeredHashesBeforeAuthentication);

        $this->subject->authenticate($registeredHash);

        $registeredHashesAfterAuthentication = file($this->filename);

        self::assertIsArray($registeredHashesAfterAuthentication);
        self::assertNotContains($registeredHash, $registeredHashesAfterAuthentication);
    }

    /**
     * @test
     */
    public function registerWritesHashToTransientFile(): void
    {
        $actual = $this->subject->register();

        $registeredHashesBeforeAuthentication = file($this->filename);

        self::assertIsArray($registeredHashesBeforeAuthentication);
        self::assertContains($actual, $registeredHashesBeforeAuthentication);
    }
}
