..  include:: /Includes.rst.txt

..  _exception-handler:

=================
Exception handler
=================

The core component of this extension is to modify the default
:ref:`debug exception handler <t3coreapi:error-handling-debug-exception-handler>`
from TYPO3 core. For this, the :php:`\EliasHaeussler\Typo3Solver\Error\AiSolverExceptionHandler`
class is shipped with the extension.

..  _exception-handler-configuration:

Configuration
=============

In order to active the modified exception handler, it must be explicitly
configured at :php:`$GLOBALS['TYPO3_CONF_VARS']['SYS']['debugExceptionHandler']`.
This can be done in the :ref:`system configuration <t3coreapi:configuration-files>`
file :file:`config/system/settings.php` (formerly
:file:`public/typo3conf/LocalConfiguration.php`):

..  code-block:: php
    :caption: config/system/settings.php

    return [
        'SYS' => [
            'debugExceptionHandler' => 'EliasHaeussler\\Typo3Solver\\Error\\AiSolverExceptionHandler',
        ],
    ];

Once configured, the exception handler tries to provide a solution for
the current exception and shows it on the exception page. Depending on
the handled exception, there are various types of problem solving.

..  attention::

    For the exception handler to work as expected, make sure you have
    added an :ref:`API key <api-key>`. This is an essential step,
    otherwise the exception handler won't be able to provide AI generated
    solutions and just shows a warning about the missing API key.

..  _types-of-problem-solving:

Types of problem solving
========================

..  _streamed-solution:

Streamed solution
-----------------

..  versionadded:: 0.2.0

    `Feature: #64 - Switch to chat completions and implement solution streams <https://github.com/eliashaeussler/typo3-solver/pull/64>`__

This variant is automatically used if an exception is triggered in a stage
where TYPO3 bootstrapping is already done and therefore a
:ref:`service container <t3coreapi:Dependency-Injection>` exists. In this
case, the solution is provided asynchronous from an
`event stream <https://developer.mozilla.org/en-US/docs/Web/API/Server-sent_events>`__
by a custom middleware :samp:`/tx_solver/solution`.

..  figure:: /Assets/Examples/streamed-solution.gif
    :alt: Video showing a solution stream on an error page

    Solution stream on error page

..  note::

    The extension provides two early-staged **middlewares**:

    1.  Ensure solution stream is possible by requesting :samp:`/tx_solver/ping`
    2.  Provide streamed solution by requesting :samp:`/tx_solver/solution`

    Both middlewares are registered to be handled very early, ideally prior to
    all other middlewares. If you register additional middlewares on your own,
    make sure they don't conflict with the middlewares provided by this extension.

    Read more at :ref:`middlewares`.

..  _default-solution:

Default solution
----------------

There are cases where solution streaming is not possible. For example, if
the exception to handle was triggered very early in the bootstrapping
process. Those cases prevent solution streaming as it requires a functioning
bootstrapping and a successfully built service container.

If solution streaming is not possible, the solution is provided synchronous.
This way is less convenient because – depending on the
:ref:`configured attributes <extension-configuration>` – requests to
the AI provider may take relatively long, up to several minutes. During the request,
no actions can be done as it would interrupt the request and therefore
abort problem solving.

..  figure:: /Assets/Examples/default-solution.png
    :alt: Error page with a synchronously provided solution

    Default solution on error page

..  note::

    You can influence long durations a little bit by modifying the attributes
    used for each request to the AI provider. All supported attributes can be changed
    in the :ref:`extension configuration <extension-configuration>`.
