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

namespace EliasHaeussler\Typo3Solver\ViewHelpers;

use Closure;
use Parsedown;

use TYPO3Fluid\Fluid;

/**
 * MarkdownToHtmlViewHelper
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
final class MarkdownToHtmlViewHelper extends Fluid\Core\ViewHelper\AbstractViewHelper
{
    use Fluid\Core\ViewHelper\Traits\CompileWithContentArgumentAndRenderStatic;

    protected $escapeChildren = false;
    protected $escapeOutput = false;

    public function initializeArguments(): void
    {
        $this->registerArgument(
            'markdown',
            'string',
            'The markdown text to convert',
        );
    }

    public static function renderStatic(
        array $arguments,
        Closure $renderChildrenClosure,
        Fluid\Core\Rendering\RenderingContextInterface $renderingContext,
    ): string {
        $markdown = $renderChildrenClosure();
        $parsedown = new Parsedown();
        $parsedown->setSafeMode(true);

        return $parsedown->text($markdown);
    }
}
