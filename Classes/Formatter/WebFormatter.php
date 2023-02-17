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

namespace EliasHaeussler\Typo3Solver\Formatter;

use EliasHaeussler\Typo3Solver\ProblemSolving;
use EliasHaeussler\Typo3Solver\View;
use Symfony\Component\Filesystem;
use TYPO3\CMS\Core;

use function count;
use function dirname;
use function explode;
use function file_exists;
use function file_get_contents;
use function is_numeric;

/**
 * WebFormatter.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
final class WebFormatter implements Formatter
{
    private const STYLESHEET_PATH = 'Resources/Public/Css/solutions.css';

    private readonly View\TemplateRenderer $renderer;

    public function __construct()
    {
        $this->renderer = new View\TemplateRenderer();
    }

    public function format(ProblemSolving\Solution\Solution $solution): string
    {
        $choices = [];

        foreach ($solution as $choice) {
            $choices[] = [
                'sections' => $this->splitIntoSections($choice->text),
            ];
        }

        return $this->renderer->render('Solution/Web', [
            'solution' => $solution,
            'choices' => $choices,
            'numberOfChoices' => count($solution),
        ]);
    }

    public function getAdditionalStyles(): string
    {
        $rootPath = dirname(__DIR__, 2);
        $stylesheetPath = Filesystem\Path::join($rootPath, self::STYLESHEET_PATH);

        if (file_exists($stylesheetPath)) {
            return (string)@file_get_contents($stylesheetPath);
        }

        return '';
    }

    /**
     * @return list<array{section: Section\CodeBlock|Section\Text, type: string}>
     */
    private function splitIntoSections(string $solutionText): array
    {
        $result = [];
        $section = null;
        $lines = Core\Utility\GeneralUtility::trimExplode(PHP_EOL, $solutionText, true);

        foreach ($lines as $content) {
            if (is_numeric($content[0])) {
                $type = 'codeBlock';
                $className = Section\CodeBlock::class;
            } else {
                $type = 'text';
                $className = Section\Text::class;
            }

            if (!($section instanceof $className)) {
                $result[] = [
                    'section' => $section = new $className(),
                    'type' => $type,
                ];
            }

            if ($section instanceof Section\CodeBlock) {
                [$line, $code] = explode(' ', $content, 2);
                $section->append(' ' . $code, (int)$line);
            } else {
                $section->append($content);
            }
        }

        return $result;
    }
}
