'use strict';

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

/**
 * List of used HTML element selectors.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
export enum Selectors
{
  solution = '.solution',
  solutionContainer = '.solution-container',
  solutionCurrentResponse = '.solution-current-response',
  solutionListItem = '.solution-list-item',
  solutionLoaderCount = '.solution-loader-count',
  solutionMaxResponses = '.solution-max-responses',
  solutionModel = '.solution-model',
  solutionPrompt = '.solution-prompt > pre',
  solutionSelector = '.solution-selector',
}
