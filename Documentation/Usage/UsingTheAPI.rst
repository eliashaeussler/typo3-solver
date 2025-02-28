..  include:: /Includes.rst.txt

..  _using-the-api:

=============
Using the API
=============

Besides the usage via the modified debug :ref:`exception handler <exception-handler>`
and the :ref:`console commands <console-commands>`, there is also a public PHP API.
It can be used to solve problems directly in PHP code.

..  php:namespace:: EliasHaeussler\Typo3Solver\ProblemSolving

..  php:class:: Solver

    Core component to solve problems by a given exception.

    ..  php:method:: solve($exception)

        Provide solution for given exception and format it according to the configured
        formatter. If a cached solution exists, the provider does not request a new
        solution.

        ..  note::

            This method is basically described at :ref:`default-solution`.

        :param Throwable $exception: Exception for which a solution is to provide.
        :returntype: :php:`string|null`

    ..  php:method:: solveStreamed($exception)

        Provide formatted solution for given exception, but use a solution stream for
        each solution delta.

        ..  note::

            This method is basically described at :ref:`streamed-solution`.

        :param Throwable $exception: Exception for which a solution is to provide.
        :returntype: :php:`Traversable<string>`

..  _api-example:

Example
=======

Create a new :php:class:`EliasHaeussler\\Typo3Solver\\ProblemSolving\\Solver`:

..  code-block:: php

    use EliasHaeussler\Typo3Solver;
    use TYPO3\CMS\Core;

    $configuration = Core\Utility\GeneralUtility::makeInstance(
        Typo3Solver\Configuration\Configuration::class,
    );

    // Use any supported formatter and provider
    $formatter = Core\Utility\GeneralUtility::makeInstance(
        Typo3Solver\Formatter\WebFormatter::class,
    );
    $solutionProvider = Core\Utility\GeneralUtility::makeInstance(
        Typo3Solver\ProblemSolving\Solution\Provider\OpenAISolutionProvider::class,
    );

    $solver = new Typo3Solver\ProblemSolving\Solver(
        $configuration,
        $formatter,
        $solutionProvider,
    );

Solve synchronously:

..  code-block:: php

    $formattedSolution = $solver->solve($exception);

Solve asynchronously:

..  code-block:: php

    foreach ($solver->solveStreamed($exception) as $solutionDelta) {
        echo $solutionDelta;
    }
