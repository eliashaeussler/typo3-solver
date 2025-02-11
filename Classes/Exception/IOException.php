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

namespace EliasHaeussler\Typo3Solver\Exception;

/**
 * IOException
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
final class IOException extends Exception
{
    public static function forConflictingParameters(string ...$parameters): self
    {
        return new self(
            \sprintf('The parameters "%s" cannot be used together.', \implode('", "', $parameters)),
            1680388489,
        );
    }

    public static function forMissingRequiredParameter(string $parameter): self
    {
        return new self(
            \sprintf('The parameter "%s" is required.', $parameter),
            1680388939,
        );
    }
}
