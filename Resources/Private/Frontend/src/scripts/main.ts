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

import {Solution} from "./ProblemSolving/Solution/Solution";
import {SolutionStream} from "./ProblemSolving/Solution/SolutionStream";

try {
  const solution: Solution = Solution.create();

  if (solution.canBeStreamed()) {
    (solution.createStream() as SolutionStream).start();
  } else {
    solution.handleSolutionSelection();
  }
} catch (e) { // eslint-disable-line sonarjs/no-ignored-exceptions
  // Intended fallthrough.
}
