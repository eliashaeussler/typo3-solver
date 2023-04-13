..  include:: /Includes.rst.txt

..  _start:

==============================================
Exception handling with AI generated solutions
==============================================

..  rst-class:: horizbuttons-tip-m

-   :ref:`configuration`
-   :ref:`exception-handler`
-   :ref:`console-commands`

:Extension key:
    `solver <https://extensions.typo3.org/extension/solver>`__

:Package name:
    `eliashaeussler/typo3-solver <https://packagist.org/packages/eliashaeussler/typo3-solver>`__

:Version:
    |release|

:Language:
    en

:Author:
    `Elias Häußler <https://haeussler.dev>`__ & contributors

:License:
    This extension documentation is published under the
    `CC BY-NC-SA 4.0 <https://creativecommons.org/licenses/by-nc-sa/4.0/>`__
    (Creative Commons) license.

----

An extension for TYPO3 CMS to solve exceptions with
:abbr:`AI (Artificial Intelligence)` generated solutions. It uses the
`OpenAI PHP client <https://github.com/openai-php/client>`__ to send a
prompt to a configured model and uses the responded completion as solution.
Several completion attributes (model, tokens, temperature, number of
completions) are configurable. It also provides a console command to solve
problems from command line.

----

**Table of Contents**

..  toctree::
    :maxdepth: 3

    Introduction/Index
    Installation/Index
    Configuration/Index
    Usage/Index
    DeveloperCorner/Index
    Migration/Index
    Contributing/Index

..  toctree::
    :hidden:

    Sitemap
    genindex
