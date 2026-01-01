<?php

declare(strict_types=1);

/*
 * This file is part of the TYPO3 CMS extension "solver".
 *
 * Copyright (C) 2023-2026 Elias Häußler <elias@haeussler.dev>
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

namespace EliasHaeussler\Typo3Solver\Tests;

use TYPO3Fluid\Fluid;

/**
 * ViewTrait
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
trait ViewTrait
{
    private function createView(string $template): Fluid\View\TemplateView
    {
        /** @noinspection HtmlRequiredLangAttribute */
        $templateSource = <<<FLUID
<html xmlns:s="http://typo3.org/ns/EliasHaeussler/Typo3Solver/ViewHelpers"
      data-namespace-typo3-fluid="true">

{$template}

</html>
FLUID;

        $context = new Fluid\Core\Rendering\RenderingContext();
        $context->getTemplatePaths()->setTemplateSource($templateSource);

        return new Fluid\View\TemplateView($context);
    }
}
