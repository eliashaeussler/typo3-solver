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

namespace EliasHaeussler\Typo3Solver\Cache\Serializer;

use ReflectionClass;
use Throwable;

/**
 * ExceptionDumper
 *
 * @author Elias Häußler <e.haeussler@familie-redlich.de>
 * @license GPL-2.0-or-later
 *
 * @internal
 *
 * @phpstan-type ExceptionArray array{
 *     className: class-string<Throwable>,
 *     exception: array{
 *         message: string,
 *         code: int,
 *         file: string,
 *         line: int,
 *     },
 * }
 */
final class ExceptionSerializer
{
    /**
     * @template T of Throwable
     * @phpstan-param T $exception
     * @phpstan-return ExceptionArray
     */
    public function serialize(Throwable $exception): array
    {
        return [
            'className' => $exception::class,
            'exception' => [
                'message' => $exception->getMessage(),
                'code' => $exception->getCode(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ],
        ];
    }

    /**
     * @phpstan-param ExceptionArray $exceptionArray
     */
    public function deserialize(array $exceptionArray): Throwable
    {
        $className = $exceptionArray['className'];
        $properties = $exceptionArray['exception'];

        // Create exception class
        $reflectionClass = new ReflectionClass($className);
        $exception = $reflectionClass->newInstanceWithoutConstructor();

        // Restore exception properties
        foreach ($properties as $propertyName => $propertyValue) {
            if ($reflectionClass->hasProperty($propertyName)) {
                $reflectionProperty = $reflectionClass->getProperty($propertyName);
                $reflectionProperty->setAccessible(true);
                $reflectionProperty->setValue($exception, $propertyValue);
            }
        }

        return $exception;
    }
}
