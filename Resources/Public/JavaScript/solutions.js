const solution = document.querySelector('.solution');

/**
 * Start event stream to fetch solution for given exception.
 *
 * @param {string} exceptionId Identifier for a cached exception
 */
function startEventStream(exceptionId)
{
    const solutionContainer = solution.querySelector('.solution-container');
    const solutionModel = solution.querySelector('.solution-model');
    const solutionMaxChoices = solution.querySelector('.solution-max-choices');
    const solutionPrompt = solution.querySelector('.solution-prompt > pre');
    const solutionLoadingCount = solution.querySelector('.solution-loading-count');

    // Create event source
    const eventSource = new EventSource('/tx_solver/solution?exception=' + exceptionId);

    // Enable source streaming
    solution.classList.add('solution-streaming');

    // Handle solution delta event
    eventSource.addEventListener('solutionDelta', (event) => {
        const data = JSON.parse(event.data);
        const {model, numberOfChoices, numberOfPendingChoices, prompt} = data.data;

        // Replace solution list
        solutionContainer.innerHTML = data.content;

        // Replace solution data
        solutionModel.innerHTML = model;
        solutionMaxChoices.innerHTML = numberOfChoices;
        solutionPrompt.innerHTML = prompt;

        // Replace number of choices
        if (numberOfChoices > 1) {
            solutionLoadingCount.innerHTML = numberOfPendingChoices;
        }
    });

    // Handle solution finished event
    eventSource.addEventListener('solutionFinished', () => {
        solution.classList.remove('solution-streaming');
        solution.classList.add('solution-provided');

        eventSource.close();

        handleSolutionSelection();
    });
}

function handleSolutionSelection()
{
    const solutionListItems = solution.querySelectorAll('.solution-list-item');
    const solutionCurrentChoice = solution.querySelector('.solution-current-choice');

    solutionListItems.forEach((solutionListItem) => {
        const index = parseInt(solutionListItem.dataset.solutionChoiceIndex);
        const selector = solutionListItem.querySelector('.solution-selector');

        selector.addEventListener('input', (event) => {
            solutionCurrentChoice.innerHTML = (index + 1).toString();
            solutionListItem.setAttribute('aria-hidden', 'false');

            solutionListItems.forEach((otherSolutionListItem) => {
                if (otherSolutionListItem !== solutionListItem) {
                    otherSolutionListItem.setAttribute('aria-hidden', 'true');
                }
            })
        });
    });
}

if (solution?.dataset.exceptionId && !solution?.classList.contains('solution-provided')) {
    startEventStream(solution.dataset.exceptionId);
} else {
    handleSolutionSelection();
}
