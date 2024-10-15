..  include:: /Includes.rst.txt

..  _caching:

=======
Caching
=======

Generated solutions can be cached if the solution provider in
use is cacheable (see :php:meth:`\EliasHaeussler\Typo3Solver\ProblemSolving\Solution\Provider\SolutionProvider::isCacheable`).
For this, a filesystem-based solution cache is implemented. In
addition, an exception cache is provided which stores exceptions
when :ref:`solution streaming <streamed-solution>` is active.

..  note::

    All caches are low-level caches and cannot be configured.

..  php:namespace:: EliasHaeussler\Typo3Solver\Cache

..  php:class:: ExceptionsCache

    Low-level cache manager for exceptions. This class is only
    used in combination with :ref:`solution streaming <streamed-solution>`.

    ..  note::

        This cache writes to :file:`var/cache/data/tx_solver/exceptions.php`.

    ..  php:method:: get($entryIdentifier)

        Get exception by cache identifier. If no cache with the
        given identifier exists, :php:`null` is returned.

        :param string $entryIdentifier: Cache identifier of the exception to look up
        :returntype: Throwable|null

    ..  php:method:: getIdentifier($exception)

        Get calculated exception identifier of the given exception.

        :param Throwable $exception: Exception to calculate a cache identifier
        :returntype: string

    ..  php:method:: set($exception)

        Add the given exception to the exceptions cache and
        return the associated cache identifier.

        :param Throwable $exception: The exception to be cached
        :returntype: string

    ..  php:method:: flush()

        Remove all cached exceptions.

..  php:class:: SolutionsCache

    Low-level cache manager for solutions. This class is used in
    combination with :php:class:`EliasHaeussler\\Typo3Solver\\ProblemSolving\\Solution\\Provider\\CacheSolutionProvider`.

    ..  note::

        This cache writes to :file:`var/cache/data/tx_solver/solutions.php`.

    ..  php:method:: get($problem)

        Get cached solution for the given problem. If no solution
        exists in cache, :php:`null` is returned.

        :param EliasHaeussler\Typo3Solver\ProblemSolving\Problem\Problem $problem: Problem to get a cached solution for
        :returntype: EliasHaeussler\\Typo3Solver\\ProblemSolving\\Solution\\Solution|null

    ..  php:method:: set($problem, $solution)

        Add the given solution to the solutions cache and use the
        given problem to generate the cache identifier.

        :param EliasHaeussler\Typo3Solver\ProblemSolving\Problem\Problem $problem: The problem of the solution to be cached
        :param EliasHaeussler\Typo3Solver\ProblemSolving\Solution\Solution $solution: The solution to be cached

    ..  php:method:: flush()

        Remove all cached solutions.

..  seealso::

    View the sources on GitHub:

    -   `ExceptionsCache <https://github.com/eliashaeussler/typo3-solver/blob/main/Classes/Cache/ExceptionsCache.php>`__
    -   `SolutionsCache <https://github.com/eliashaeussler/typo3-solver/blob/main/Classes/Cache/SolutionsCache.php>`__
