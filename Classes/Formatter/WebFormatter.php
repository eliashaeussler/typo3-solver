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
        $numberOfChoices = count($solution);
        $formattedChoices = '';

        foreach ($solution as $index => $choice) {
            $formattedChoice = '';

            foreach ($this->splitIntoSections($choice->text) as $section) {
                if ($section instanceof Section\CodeBlock) {
                    $formattedChoice .= $this->formatCodeBlock($section->get());
                } else {
                    $formattedChoice .= $this->formatText($section->get());
                }
            }

            if ($numberOfChoices > 1) {
                $formattedChoice = $this->renderMultipleChoices($formattedChoice, $index, $numberOfChoices);
            } else {
                $formattedChoice = $this->renderSingleChoice($formattedChoice);
            }

            $formattedChoices .= '<div class="solution-choice">' . $formattedChoice . '</div>';
        }

        return $this->renderer->render('Solution/Web', [
            'solution' => $solution,
            'numberOfChoices' => $numberOfChoices,
            'formattedChoices' => $formattedChoices,
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

    private function renderSingleChoice(string $formattedChoice): string
    {
        return $this->renderer->render('Choice/SingleChoice', [
            'formattedChoice' => $formattedChoice,
        ]);
    }

    private function renderMultipleChoices(string $formattedChoice, int $index, int $numberOfChoices): string
    {
        return $this->renderer->render('Choice/MultipleChoices', [
            'formattedChoice' => $formattedChoice,
            'numberOfChoices' => $numberOfChoices,
            'index' => $index,
            'prevIndex' => $index > 0 ? $index - 1 : $numberOfChoices - 1,
            'nextIndex' => $index < ($numberOfChoices - 1) ? $index + 1 : 0,
        ]);
    }

    private function formatText(string $text): string
    {
        return $this->renderer->render('Section/Text', [
            'text' => $text,
        ]);
    }

    /**
     * @param array<int, string> $lines
     */
    private function formatCodeBlock(array $lines): string
    {
        return $this->renderer->render('Section/CodeBlock', [
            'lines' => $lines,
        ]);
    }

    /**
     * @return list<Section\CodeBlock|Section\Text>
     */
    private function splitIntoSections(string $solutionText): array
    {
        $result = [];
        $section = null;
        $lines = Core\Utility\GeneralUtility::trimExplode(PHP_EOL, $solutionText, true);

        foreach ($lines as $content) {
            if (is_numeric($content[0])) {
                $type = Section\CodeBlock::class;
            } else {
                $type = Section\Text::class;
            }

            if (!($section instanceof $type)) {
                $result[] = $section = new $type();
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
