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

namespace EliasHaeussler\Typo3Solver\ViewHelpers;

use TYPO3Fluid\Fluid;

/**
 * DateViewHelper
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
final class DateViewHelper extends Fluid\Core\ViewHelper\AbstractViewHelper
{
    public function initializeArguments(): void
    {
        $this->registerArgument(
            'date',
            \DateTimeInterface::class,
            'The date to format',
            true,
        );
        $this->registerArgument(
            'readable',
            'boolean',
            'Whether to make output better readable',
            false,
            false,
        );
    }

    /**
     * @param array{date: \DateTimeInterface, readable: bool} $arguments
     *
     * @todo Migrate to render() once support for TYPO3 v12 is dropped
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        Fluid\Core\Rendering\RenderingContextInterface $renderingContext,
    ): string {
        $date = $arguments['date'];
        $readable = $arguments['readable'];

        // Early return if output should not be better readable
        if (!$readable) {
            return $date->format('d.m.Y H:i:s');
        }

        $now = new \DateTimeImmutable();
        $delta = $now->getTimestamp() - $date->getTimestamp();
        $diff = $date->diff($now);

        if ($delta < 5) {
            return 'a few moments ago';
        }
        if ($delta < 60) {
            return self::renderDiff($diff->s, 'second');
        }
        if ($delta < 60 * 60) {
            return self::renderDiff($diff->i, 'minute');
        }
        if ($delta < 60 * 60 * 24) {
            return self::renderDiff($diff->h, 'hour');
        }
        if ($delta < 60 * 60 * 24 * 7) {
            return self::renderDiff($diff->d, 'day');
        }

        return $date->format('d.m.Y');
    }

    private static function renderDiff(int $duration, string $unit): string
    {
        return \sprintf('%d %s%s ago', $duration, $unit, $duration === 1 ? '' : 's');
    }
}
