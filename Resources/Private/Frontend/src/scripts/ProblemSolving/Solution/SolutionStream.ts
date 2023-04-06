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

import {Solution} from "./Solution";
import {Selectors} from "../../Enums/Selectors";
import {Classes} from "../../Enums/Classes";
import {Events} from "../../Enums/Events";

/**
 * SolutionStream.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-2.0-or-later
 */
export class SolutionStream
{
  private readonly solutionContainer: HTMLElement;
  private readonly solutionModel: HTMLElement;
  private readonly solutionMaxChoices: HTMLElement;
  private readonly solutionPrompt: HTMLElement;
  private readonly solutionLoaderCount: HTMLElement;
  private eventSource: EventSource|null = null;

  constructor(
    private readonly solution: Solution,
    private readonly exceptionId: string,
  ) {
    this.solutionContainer = this.solution.element.querySelector(Selectors.solutionContainer) as HTMLElement;
    this.solutionModel = this.solution.element.querySelector(Selectors.solutionModel) as HTMLElement;
    this.solutionMaxChoices = this.solution.element.querySelector(Selectors.solutionMaxChoices) as HTMLElement;
    this.solutionPrompt = this.solution.element.querySelector(Selectors.solutionPrompt) as HTMLElement;
    this.solutionLoaderCount = this.solution.element.querySelector(Selectors.solutionLoaderCount) as HTMLElement;
  }

  /**
   * Start a new solution stream.
   */
  public start(): void
  {
    // Early return if solution stream is active
    if (this.eventSource !== null && !this.eventSource.CLOSED) {
      return;
    }

    // Start event source
    this.eventSource = new EventSource('/tx_solver/solution?exception=' + this.exceptionId);
    this.solution.element.classList.add(Classes.solutionStreaming);

    // Handle caret
    setInterval(this.toggleCaret.bind(this), 750);

    // Handle events
    this.eventSource.addEventListener(Events.solutionDelta, this.handleSolutionDelta.bind(this));
    this.eventSource.addEventListener(Events.solutionError, this.handleSolutionError.bind(this));
    this.eventSource.addEventListener(Events.solutionFinished, this.handleSolutionFinished.bind(this));
  }

  /**
   * Apply solution delta to solution element.
   *
   * @param {MessageEvent} event Data of streamed event "solutionDelta"
   * @private
   */
  private handleSolutionDelta(event: MessageEvent<string>): void
  {
    const data: SolutionDeltaResponse = JSON.parse(event.data);
    const {model, numberOfChoices, numberOfPendingChoices, prompt} = data.data;

    // Replace solution list
    this.solutionContainer.innerHTML = data.content;

    // Replace solution data
    this.solutionModel.innerHTML = model;
    this.solutionMaxChoices.innerHTML = numberOfChoices.toString();
    this.solutionPrompt.innerHTML = prompt;

    // Replace number of choices
    if (numberOfPendingChoices > 1) {
      this.solutionLoaderCount.innerHTML = numberOfPendingChoices.toString();
    }
  }

  /**
   * Show error message on solution stream error.
   *
   * @param {MessageEvent} event Data of streamed event "solutionError"
   * @private
   */
  private handleSolutionError(event: MessageEvent): void
  {
    const data: SolutionErrorResponse = JSON.parse(event.data);

    this.solution.element.outerHTML = data.content;
  }

  /**
   * Finalize solution stream on finished solution stream.
   *
   * @private
   */
  private handleSolutionFinished(): void
  {
    this.solution.element.classList.remove(Classes.solutionStreaming);
    this.solution.element.classList.add(Classes.solutionProvided);

    // Close open event stream
    this.eventSource?.close();
    this.eventSource = null;

    // Handle solution selections
    this.solution.handleSolutionSelection();
  }

  /**
   * Toggle blinking caret during solution stream.
   *
   * @private
   */
  private toggleCaret(): void
  {
    if (this.solution.element.classList.contains(Classes.solutionCaretVisible)) {
      this.solution.element.classList.remove(Classes.solutionCaretVisible);
    } else {
      this.solution.element.classList.add(Classes.solutionCaretVisible);
    }
  }
}
