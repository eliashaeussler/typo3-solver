..  include:: /Includes.rst.txt

..  _console-commands:

================
Console commands
================

The extension provides the following console commands:

..  _solver-solve:

`solver:solve`
==============

Next to the :ref:`exception handler <exception-handler>` integration,
one can also explicitly solve problems using the provided console
command `solver:solve`.

..  tabs::

    ..  group-tab:: Composer-based installation

        ..  code-block:: bash

            vendor/bin/typo3 solver:solve [<problem>] [options]

    ..  group-tab:: Legacy installation

        ..  code-block:: bash

            typo3/sysext/core/bin/typo3 solver:solve [<problem>] [options]

Problems can be solved in two ways on the command line:

1.  Pass the :ref:`problem <solver-solve-problem>` (= exception message) and
    additional metadata such as exception :ref:`code <solver-solve-code>`,
    :ref:`file <solver-solve-file>` and :ref:`line <solver-solve-line>`. By
    using this way, EXT:solver will create a dummy exception and pass it to
    the solution provider.
2.  Pass an exception cache identifier to solve a cached exception. This way
    is more accurate as it restores the original exception and passes it to
    the solution provider.

..  tip::

    You can find the exception cache identifier on exception pages. It is
    assigned as `data-exception-id` attribute to the solution container element.

..  note::

    This command is not :ref:`schedulable <t3coreapi:schedulable>`.

The following input parameters are available:

..  _solver-solve-problem:

..  confval:: problem

    :Required: false
    :type: string
    :Default: none

    The exception message to solve.

    ..  note::

        You must either pass the :ref:`problem <solver-solve-problem>` argument or
        :ref:`--identifier <solver-solve-identifier>` option.

    Example:

    ..  tabs::

        ..  group-tab:: Composer-based installation

            ..  code-block:: bash

                vendor/bin/typo3 solver:solve "No TypoScript record found!"

        ..  group-tab:: Legacy installation

            ..  code-block:: bash

                typo3/sysext/core/bin/typo3 solver:solve "No TypoScript record found!"

..  _solver-solve-identifier:

..  confval:: -i|--identifier

    :Required: false
    :type: string
    :Default: none

    An alternative cache identifier to load an exception from cache.

    ..  note::

        You must either pass the :ref:`problem <solver-solve-problem>` argument or
        :ref:`--identifier <solver-solve-identifier>` option.

    Example:

    ..  tabs::

        ..  group-tab:: Composer-based installation

            ..  code-block:: bash

                vendor/bin/typo3 solver:solve -i c98d277467ab5da857483dff2b1d267d36c0c24a

        ..  group-tab:: Legacy installation

            ..  code-block:: bash

                typo3/sysext/core/bin/typo3 solver:solve -i c98d277467ab5da857483dff2b1d267d36c0c24a

..  _solver-solve-code:

..  confval:: -c|--code

    :Required: false
    :type: integer
    :Default: none

    Optional exception code.

    ..  note::

        This option is only respected in combination with the
        :ref:`problem <solver-solve-problem>` argument.

    Example:

    ..  tabs::

        ..  group-tab:: Composer-based installation

            ..  code-block:: bash

                vendor/bin/typo3 solver:solve "No TypoScript record found!" -c 1294587218

        ..  group-tab:: Legacy installation

            ..  code-block:: bash

                typo3/sysext/core/bin/typo3 solver:solve "No TypoScript record found!" -c 1294587218

..  _solver-solve-file:

..  confval:: -f|--file

    :Required: false
    :type: string
    :Default: none

    Optional file where the exception occurs.

    ..  note::

        This option is only respected in combination with the
        :ref:`problem <solver-solve-problem>` argument.

    Example:

    ..  tabs::

        ..  group-tab:: Composer-based installation

            ..  code-block:: bash

                vendor/bin/typo3 solver:solve "No TypoScript record found!" -f /var/www/html/vendor/typo3/cms-frontend/Classes/Controller/TypoScriptFrontendController.php

        ..  group-tab:: Legacy installation

            ..  code-block:: bash

                typo3/sysext/core/bin/typo3 solver:solve "No TypoScript record found!" -f /var/www/html/vendor/typo3/cms-frontend/Classes/Controller/TypoScriptFrontendController.php

..  _solver-solve-line:

..  confval:: -l|--line

    :Required: false
    :type: integer
    :Default: none

    Optional line number within the given file.

    ..  note::

        This option is only respected in combination with the
        :ref:`problem <solver-solve-problem>` argument.

    Example:

    ..  tabs::

        ..  group-tab:: Composer-based installation

            ..  code-block:: bash

                vendor/bin/typo3 solver:solve "No TypoScript record found!" -l 1190

        ..  group-tab:: Legacy installation

            ..  code-block:: bash

                typo3/sysext/core/bin/typo3 solver:solve "No TypoScript record found!" -l 1190

..  _solver-solve-refresh:

..  confval:: -r|--refresh

    :Required: false
    :type: boolean
    :Default: false

    Refresh a cached solution (removes the cached solution and requests a new solution).

    Example:

    ..  tabs::

        ..  group-tab:: Composer-based installation

            ..  code-block:: bash

                vendor/bin/typo3 solver:solve "No TypoScript record found!" --refresh

        ..  group-tab:: Legacy installation

            ..  code-block:: bash

                typo3/sysext/core/bin/typo3 solver:solve "No TypoScript record found!" --refresh

..  _solver-solve-json:

..  confval:: -j|--json

    :Required: false
    :type: boolean
    :Default: false

    Print solution as JSON.

    Example:

    ..  tabs::

        ..  group-tab:: Composer-based installation

            ..  code-block:: bash

                vendor/bin/typo3 solver:solve "No TypoScript record found!" --json

        ..  group-tab:: Legacy installation

            ..  code-block:: bash

                typo3/sysext/core/bin/typo3 solver:solve "No TypoScript record found!" --json

..  _solver-list-models:

`solver:list-models`
====================

The command `solver:list-models` can be used to list all available
models for the configured OpenAI :ref:`API key <api-key>`.

..  note::

    EXT:solver uses the `chat completion <https://platform.openai.com/docs/guides/chat>`__
    component to generate solutions. You must select a model being available
    with the chat completion component only.

    All available models are also listed in the
    `OpenAI documentation <https://platform.openai.com/docs/models>`__.

..  note::

    This command is not :ref:`schedulable <t3coreapi:schedulable>`.

..  tabs::

    ..  group-tab:: Composer-based installation

        ..  code-block:: bash

            vendor/bin/typo3 solver:list-models

    ..  group-tab:: Legacy installation

        ..  code-block:: bash

            typo3/sysext/core/bin/typo3 solver:list-models

..  _solver-cache-flush:

`solver:cache:flush`
====================

Every solution is cached to reduce the amount of requests sent by the
OpenAI client. In order to flush the solution cache or remove single
cache entries, the command `solver:cache:flush` can be used.

..  tabs::

    ..  group-tab:: Composer-based installation

        ..  code-block:: bash

            vendor/bin/typo3 solver:cache:flush [<identifier>]

    ..  group-tab:: Legacy installation

        ..  code-block:: bash

            typo3/sysext/core/bin/typo3 solver:cache:flush [<identifier>]

The following input parameters are available:

..  confval:: identifier

    :Required: false
    :type: string
    :Default: none

    An optional cache identifier to remove only a single cached solution.
    This is especially helpful to require a new solution for a specific
    problem while keeping other solutions cached.

    ..  tip::

        The cache identifier can be found on the error page next to the
        used OpenAI model. Hover over the cache date to make it visible.

    Example:

    ..  tabs::

        ..  group-tab:: Composer-based installation

            ..  code-block:: bash

                vendor/bin/typo3 solver:cache:flush 65e89b311899aa4728a4c1bced1d6f6335674422

        ..  group-tab:: Legacy installation

            ..  code-block:: bash

                typo3/sysext/core/bin/typo3 solver:cache:flush 65e89b311899aa4728a4c1bced1d6f6335674422
