..  include:: /Includes.rst.txt

..  _template-rendering:

==================
Template rendering
==================

Since exception handling may happen on a very low level where
TYPO3 bootstrapping is not completed yet, this extension
ships with a very generic template renderer.

..  note::

    This is an internal component and not part of the public API.

..  php:namespace:: EliasHaeussler\Typo3Solver\View

..  php:class:: TemplateRenderer

    Generic low-level renderer for Fluid templates shipped with
    EXT:solver. This class is not intended for use anywhere else
    than in the extension itself.

    ..  php:method:: render($templatePath, $variables = [])

        Render a given template with optional variables. The
        template path must be relative to
        :file:`EXT:solver/Resources/Private/Templates`.

        :param string $exception: Path to the template to be rendered
        :param array $variables: Optional template variables
        :returntype: string

..  _view-helpers:

View helpers
============

The extension provides two additional view helpers.

..  php:namespace:: EliasHaeussler\Typo3Solver\ViewHelpers

..  php:class:: DateViewHelper

    View helper to format a given date, either by a fixed format
    (:php:`d.m.Y H:i:s`) or as human readable relative date.

    Examples:

    ..  code-block:: html

        <solver:date date="{date}" /> <!-- 12.04.2023 19:51:02 -->

    ..  code-block:: html

        <solver:date date="{date}" readable="1" /> <!-- 2 hours ago -->

..  php:class:: MarkdownToHtmlViewHelper

    View helper to convert a given Markdown string to HTML. It
    uses the `erusev/parsedown <https://github.com/erusev/parsedown>`__
    library internally. In addition, resolved HTML can also be
    modified by replacing line numbers in code snippets to
    match the expected structure of code examples on error pages.

    Examples:

    ..  code-block:: html

        <solver:markdownToHtml markdown="{markdown}" />

    ..  code-block:: html

        <solver:markdownToHtml markdown="{markdown}" replaceLineNumbersInCodeSnippets="1" />

..  seealso::
    View the sources on GitHub:

    -   `DateViewHelper <https://github.com/eliashaeussler/typo3-solver/blob/main/Classes/ViewHelpers/DateViewHelper.php>`__
    -   `MarkdownToHtmlViewHelper <https://github.com/eliashaeussler/typo3-solver/blob/main/Classes/ViewHelpers/MarkdownToHtmlViewHelper.php>`__
