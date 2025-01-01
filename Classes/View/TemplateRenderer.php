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

namespace EliasHaeussler\Typo3Solver\View;

use TYPO3Fluid\Fluid;

/**
 * TemplateRenderer
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 * @internal
 */
final class TemplateRenderer
{
    private readonly Fluid\Core\Rendering\RenderingContextInterface $renderingContext;

    public function __construct()
    {
        $this->renderingContext = $this->createRenderingContext();
    }

    /**
     * @param array<string, mixed> $variables
     */
    public function render(string $templatePath, array $variables = []): string
    {
        $view = new Fluid\View\TemplateView($this->renderingContext);
        $view->assignMultiple($variables);

        return $view->render($templatePath);
    }

    private function createRenderingContext(): Fluid\Core\Rendering\RenderingContextInterface
    {
        $rootPath = \dirname(__DIR__, 2) . '/Resources/Private';
        $renderingContext = new Fluid\Core\Rendering\RenderingContext();

        $templatePaths = $renderingContext->getTemplatePaths();
        $templatePaths->setTemplateRootPaths([$rootPath . '/Templates']);
        $templatePaths->setPartialRootPaths([$rootPath . '/Partials']);

        return $renderingContext;
    }
}
