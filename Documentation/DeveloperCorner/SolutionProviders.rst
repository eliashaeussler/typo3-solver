..  include:: /Includes.rst.txt

..  _solution-providers:

==================
Solution providers
==================

The most relevant components when it comes to the actual problem
solving are solution providers. Each solution provider describes a
way to solve a given problem.

..  php:namespace:: EliasHaeussler\Typo3Solver\ProblemSolving\Solution\Provider

..  php:interface:: SolutionProvider

    Interface for solution providers used to provide a solution for
    a given problem. Solution providers can be combined with
    :php:class:`EliasHaeussler\\Typo3Solver\\ProblemSolving\\Solution\\Provider\\CacheSolutionProvider`
    if they are cacheable.

    ..  php:staticmethod:: create()

        Create a new instance of the solution provider. This is
        mainly used on a low level basis where dependency injection
        is not available.

        :returns: An instance of the current solution provider.

    ..  php:method:: getSolution($problem)

        Provide a solution for the given problem.

        :param EliasHaeussler\Typo3Solver\ProblemSolving\Problem\Problem $problem: The problem to be solved
        :returntype: EliasHaeussler\\Typo3Solver\\ProblemSolving\\Solution\\Solution
        :throws: EliasHaeussler\\Typo3Solver\\Exception\\UnableToSolveException

    ..  php:method:: canBeUsed($exception)

        Define whether the solution provider can handle the given
        exception. This is especially useful to skip problem solving
        for some exceptions whose solution is already known or is
        too specific to solve.

        :param Throwable $exception: The exception to test for compatibility
        :returntype: bool

    ..  php:method:: isCacheable()

        Define whether solutions provided by this solution provider
        should be cached when using the solution provider in combination
        with :php:class:`EliasHaeussler\\Typo3Solver\\ProblemSolving\\Solution\\Provider\\CacheSolutionProvider`.

        :returntype: bool

    ..  php:method:: listModels($includeUnsupported = false)

        List all AI models by the underlying AI provider. By default,
        only supported models are returned.

        :param bool $includeUnsupported: Define whether to return unsupported models as well
        :returntype: :php:`list<EliasHaeussler\Typo3Solver\ProblemSolving\Solution\Provider\Model\AiModel>`

..  php:interface:: StreamedSolutionProvider

    Extended interface for solution providers that are able to stream
    solutions. Read more about a practical use case of this interface
    at :ref:`streamed-solution`.

    ..  note::

        This interface extends the default
        :php:interface:`EliasHaeussler\\Typo3Solver\\ProblemSolving\\Solution\\Provider\\SolutionProvider`.

    ..  php:method:: getStreamedSolution($problem)

        Provide a solution stream for the given problem. The stream is
        returned as an instance of :php:`\Traversable`, while
        each traversed item is an instance of
        :php:`\EliasHaeussler\Typo3Solver\ProblemSolving\Solution\Solution`.

        :param EliasHaeussler\Typo3Solver\ProblemSolving\Problem\Problem $problem: The problem to be solved
        :returntype: :php:`Traversable<EliasHaeussler\Typo3Solver\ProblemSolving\Solution\Solution>`
        :throws: EliasHaeussler\\Typo3Solver\\Exception\\UnableToSolveException

..  _default-providers:

Default providers
=================

The extension ships with two default providers:

..  php:class:: OpenAISolutionProvider

    Default provider used to generate solutions using the configured
    OpenAI :ref:`model <extconf-attributes-model>`. It uses the
    `chat completion <https://platform.openai.com/docs/guides/chat>`__
    endpoint and is therefore streamable.

    ..  note::

        Streamed solutions always provide the complete solution delivered
        until the current solution delta for each iteration.

..  php:class:: GeminiSolutionProvider

    Solution provider used to generate solutions using the configured
    Google Gemini :ref:`model <extconf-attributes-model>`. It uses the
    `Text <https://ai.google.dev/api/rest/v1/models/streamGenerateContent>`__
    endpoint and is therefore streamable.

    ..  note::

        Streamed solutions always provide the complete solution delivered
        until the current solution delta for each iteration.

In addition, there's also a cached provider. It decorates a concrete solution
provider with an additional cache layer. This avoids too many requests to
the OpenAI endpoint.

..  php:class:: CacheSolutionProvider

    This provider decorates a concrete solution provider with an additional
    cache layer. The concrete solution provider must be provided in the
    :php:meth:`\EliasHaeussler\Typo3Solver\ProblemSolving\Solution\Provider\SolutionProvider::create`
    method.

    ..  note::

        Read more at :ref:`caching`.

..  seealso::
    View the sources on GitHub:

    -   `SolutionProvider <https://github.com/eliashaeussler/typo3-solver/blob/main/Classes/ProblemSolving/Solution/Provider/SolutionProvider.php>`__
    -   `StreamedSolutionProvider <https://github.com/eliashaeussler/typo3-solver/blob/main/Classes/ProblemSolving/Solution/Provider/StreamedSolutionProvider.php>`__
    -   `OpenAISolutionProvider <https://github.com/eliashaeussler/typo3-solver/blob/main/Classes/ProblemSolving/Solution/Provider/OpenAISolutionProvider.php>`__
    -   `GeminiSolutionProvider <https://github.com/eliashaeussler/typo3-solver/blob/main/Classes/ProblemSolving/Solution/Provider/GeminiSolutionProvider.php>`__
    -   `CacheSolutionProvider <https://github.com/eliashaeussler/typo3-solver/blob/main/Classes/ProblemSolving/Solution/Provider/CacheSolutionProvider.php>`__
