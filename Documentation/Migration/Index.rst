..  include:: /Includes.rst.txt

..  _migration:

=========
Migration
=========

This section lists required migration steps for upgrades between
major versions.

..  _0_1_x_-_0_2_x:

0.1.x → 0.2.x
=============

..  _migration-chat-completion-component:

Chat completion component
-------------------------

The used OpenAI component changed from text completion to chat completion.

-   Migrate the used model in your :ref:`extension configuration <extconf-attributes-model>`.
    The new default model is `gpt-3.5-turbo-0301`.

..  _migration-solution-stream:

Solution stream
---------------

Solutions are now streamed to exception pages.

-   Migrate custom solution providers to implement
    :php:interface:`EliasHaeussler\\Typo3Solver\\ProblemSolving\\Solution\\Provider\\StreamedSolutionProvider`.
-   Note the modified DOM structure for solutions on exception pages.

..  _migration-di:

Dependency injection
--------------------

Several classes are now ready for dependency injection.

-   Migrate custom classes to use dependency injection.
-   Implement new static factory method :php:`create()` within custom solution
    providers and prompts.
-   Make sure custom classes can be used without dependency injection as well,
    since exception handling may happen on a very low level where DI is not
    available (yet).
