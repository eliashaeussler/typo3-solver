..  include:: /Includes.rst.txt

..  _formatters:

==========
Formatters
==========

Formatters are used to make a generated solution visible to the user.

..  php:namespace:: EliasHaeussler\Typo3Solver\Formatter

..  php:interface:: Formatter

    Interface for formatters that convert a given problem and its
    solution into a readable format.

    ..  php:method:: format($problem, $solution)

        Format given problem and solution to string.

        :param EliasHaeussler\Typo3Solver\ProblemSolving\Problem\Problem $problem: The problem to be formatted
        :param EliasHaeussler\Typo3Solver\ProblemSolving\Solution\Solution $solution: The solution to be formatted
        :returntype: string

..  _default-formatters:

Default formatters
==================

Since exceptions are handled in various places, the extension already
ships with some default formatters:

..  php:class:: CliFormatter

    Formatter used on the command line. It displays all provided solutions.
    On verbose output, the original prompt is also displayed. In addition, if
    a solution comes from the solutions cache, the original date and cache
    identifier are displayed as well.

..  php:class:: JsonFormatter

    This formatter is used within the :ref:`solver-solve` command if the
    :ref:`solver-solve-json` option is given. It displays the solution as
    JSON string, allowing further processing, especially in CI.

..  php:class:: WebFormatter

    This formatter is used for the modified debug :ref:`exception handler <exception-handler>`.
    It adds an additional section to the error page which displays all
    solutions. In addition, the used model, generated prompt and cache
    metadata are shown.

    ..  note::

        If :ref:`solution streaming <streamed-solution>` is possible, the
        :php:class:`EliasHaeussler\\Typo3Solver\\Formatter\\WebStreamFormatter`
        is used instead on the error page.

..  php:class:: WebStreamFormatter

    On :ref:`solution streaming <streamed-solution>`, this formatter is used
    to only print the list of solutions, streaming it to the error page
    where it replaces the current solution list.

..  seealso::
    View the sources on GitHub:

    -   `Formatter <https://github.com/eliashaeussler/typo3-solver/blob/main/Classes/Formatter/Formatter.php>`__
    -   `CliFormatter <https://github.com/eliashaeussler/typo3-solver/blob/main/Classes/Formatter/CliFormatter.php>`__
        (`Fluid template <https://github.com/eliashaeussler/typo3-solver/blob/main/Resources/Private/Templates/Solution/Cli.html>`__)
    -   `JsonFormatter <https://github.com/eliashaeussler/typo3-solver/blob/main/Classes/Formatter/JsonFormatter.php>`__
    -   `WebFormatter <https://github.com/eliashaeussler/typo3-solver/blob/main/Classes/Formatter/WebFormatter.php>`__
        (`Fluid template <https://github.com/eliashaeussler/typo3-solver/blob/main/Resources/Private/Templates/Solution/Web.html>`__)
    -   `WebStreamFormatter <https://github.com/eliashaeussler/typo3-solver/blob/main/Classes/Formatter/WebStreamFormatter.php>`__
        (`Fluid template <https://github.com/eliashaeussler/typo3-solver/blob/main/Resources/Private/Templates/Solution/WebStream.html>`__)
