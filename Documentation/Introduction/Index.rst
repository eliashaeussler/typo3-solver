..  include:: /Includes.rst.txt

..  _introduction:

============
Introduction
============

..  _what-it-does:

What does it do?
================

The extension provides an exception handling to solve problems using
artificial intelligence (AI). Problem solving can be triggered in
various ways:

-   Via the :ref:`modified debug exception handler <exception-handler>`
-   Using a :ref:`console command <console-commands>`
-   Directly with the :ref:`PHP API <using-the-api>`

Problems are solved by so called *solution providers*. The extension
already ships a default :php:class:`EliasHaeussler\\Typo3Solver\\ProblemSolving\\Solution\\Provider\\OpenAISolutionProvider`,
but can also be extended by custom solution providers. Already solved
problems can be cached to avoid cost-intensive requests to OpenAI.

..  _features:

Features
========

-   Extended exception handler with AI generated solutions
-   Configurable AI completion attributes
-   Caching integration for solves problems
-   Console command to solve problems from command line
-   Customizable solution providers and prompts
-   Compatible with TYPO3 11.5 LTS, 12.4 LTS and 13.4 LTS

..  _support:

Support
=======

There are several ways to get support for this extension:

* Slack: https://typo3.slack.com/archives/C04Q3440HS6
* GitHub: https://github.com/eliashaeussler/typo3-solver/issues

..  _security-policy:

Security Policy
===============

Please read our `security policy <https://github.com/eliashaeussler/typo3-solver/blob/main/SECURITY.md>`__
if you discover a security vulnerability in this extension.

..  _license:

License
=======

This extension is licensed under
`GNU General Public License 2.0 (or later) <https://www.gnu.org/licenses/old-licenses/gpl-2.0.html>`_.
