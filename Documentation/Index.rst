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
:abbr:`AI (Artificial Intelligence)` generated solutions. It sends a
prompt to a configured AI model and uses the responded completion as solution.
Several completion attributes (model, tokens, temperature, number of
completions) are configurable. It also provides a console command to solve
problems from command line.

----

..  card-grid::
    :columns: 1
    :columns-md: 2
    :gap: 4
    :card-height: 100

    ..  card::  Introduction

        A quick overview about the main features provided by this extension.

        ..  card-footer::   :ref:`Learn more about this extension <introduction>`
            :button-style: btn btn-secondary stretched-link

    ..  card::  Installation

        Instructions on how to install this extension and which TYPO3 and PHP versions are currently supported.

        ..  card-footer::   :ref:`Getting started <installation>`
            :button-style: btn btn-secondary stretched-link

    ..  card::  Configuration

        Learn how to configure the extension in order to enable the provided features.

        ..  card-footer::   :ref:`View configuration options <configuration>`
            :button-style: btn btn-secondary stretched-link

    ..  card::  Usage

        This section describes all possible usages of this extension. Learn how to use the exception handler,
        the various console commands, and discover the PHP API.

        ..  card-footer::   :ref:`Learn how to use this extension <usage>`
            :button-style: btn btn-secondary stretched-link

    ..  card::  Developer corner

        A quick overview about all relevant classes provided by this extension.

        ..  card-footer::   :ref:`Deep dive into classes & concepts <developer-corner>`
            :button-style: btn btn-secondary stretched-link

    ..  card::  Migration

        Required migration steps when upgrading the extension to a new major version.

        ..  card-footer::   :ref:`View upgrade guide <migration>`
            :button-style: btn btn-secondary stretched-link

..  toctree::
    :hidden:

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
