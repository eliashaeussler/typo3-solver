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

use function preg_replace;
use function preg_replace_callback;

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
        $this->registerArgument(
            'replaceLineNumbersInCodeSnippets',
            'boolean',
            'Whether to replace line numbers following the error page markup',
            false,
            false,
        );
    }

    /**
     * @param array{replaceLineNumbersInCodeSnippets?: bool} $arguments
     */
    public static function renderStatic(
        array $arguments,
        Closure $renderChildrenClosure,
        Fluid\Core\Rendering\RenderingContextInterface $renderingContext,
    ): string {
        $markdown = $renderChildrenClosure();
        $replaceLineNumbers = $arguments['replaceLineNumbersInCodeSnippets'] ?? false;

        // Convert markdown to HTML
        $parsedown = new Parsedown();
        $parsedown->setSafeMode(true);
        $html = $parsedown->text($markdown);

        // Replace line numbers
        if ($replaceLineNumbers) {
            $html = self::replaceLineNumbersInCodeSnippets($html);
        }

        return $html;
    }

    private static function replaceLineNumbersInCodeSnippets(string $html): string
    {
        return preg_replace(
            '/<\/span>\s*<span/',
            '</span><span',
            preg_replace_callback(
                '/<pre><code>(.*?)<\/code><\/pre>/s',
                static fn (array $matches): string => self::replaceLineNumbersInCodeSnippet($matches[1]),
                $html,
            ) ?? $html,
        ) ?? $html;
    }

    private static function replaceLineNumbersInCodeSnippet(string $codeSnippet): string
    {
        $replacements = 0;
        $codeSnippetWithLineNumbers = preg_replace(
            '/^(\d+)\s(.*)$/m',
            '<span data-line="$1">$2</span>',
            $codeSnippet,
            count: $replacements,
        );

        if ($replacements === 0) {
            return '<pre><code>' . $codeSnippet . '</code></pre>';
        }

        return '<pre class="has-line-numbers">' . $codeSnippetWithLineNumbers . '</pre>';
    }
}
