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

import {ElementNotFoundException} from "../../Exception/ElementNotFoundException";
import {SolutionStream} from "./SolutionStream";
import {Selectors} from "../../Enums/Selectors";
import {Classes} from "../../Enums/Classes";

/**
 * Solution.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
export class Solution
{
  constructor(public readonly element: HTMLElement) {}

  public static create(): Solution
  {
    const element: Element|null = document.querySelector(Selectors.solution);

    if (!(element instanceof HTMLElement)) {
      throw ElementNotFoundException.create(Selectors.solution);
    }

    return new Solution(element);
  }

  /**
   * Check if solution streaming is possible.
   */
  public canBeStreamed(): boolean
  {
    return 'exceptionId' in this.element.dataset
      && 'streamHash' in this.element.dataset
      && !this.element.classList.contains(Classes.solutionProvided)
    ;
  }

  /**
   * Start event stream to fetch solution for given exception.
   */
  public createStream(): SolutionStream|null
  {
    // Early return if event stream is not possible
    if (!this.canBeStreamed()) {
      return null;
    }

    // Fetch exception id and stream hash
    const exceptionId: string = this.element.dataset.exceptionId as string;
    const streamHash: string = this.element.dataset.streamHash as string;

    return new SolutionStream(this, exceptionId, streamHash);
  }

  /**
   * Handle selection of a single solution from list of solutions.
   */
  public handleSolutionSelection(): void
  {
    const solutionListItems: NodeListOf<Element> = this.element.querySelectorAll(Selectors.solutionListItem);
    const solutionCurrentResponse: Element|null = this.element.querySelector(Selectors.solutionCurrentResponse);

    solutionListItems.forEach((solutionListItem: Element): void => {
      const index: number = parseInt((solutionListItem as HTMLElement).dataset.solutionResponseIndex as string);
      const selector: Element|null = solutionListItem.querySelector(Selectors.solutionSelector);

      selector?.addEventListener('input', (): void => {
        if (solutionCurrentResponse === null) {
          return;
        }

        solutionCurrentResponse.innerHTML = (index + 1).toString();
        solutionListItem.setAttribute('aria-hidden', 'false');

        solutionListItems.forEach((otherSolutionListItem: Element): void => {
          if (otherSolutionListItem !== solutionListItem) {
            otherSolutionListItem.setAttribute('aria-hidden', 'true');
          }
        })
      });
    });
  }
}
