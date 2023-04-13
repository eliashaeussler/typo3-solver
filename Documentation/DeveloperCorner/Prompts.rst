..  include:: /Includes.rst.txt

..  _prompts:

=======
Prompts
=======

AI models must be prompted to provide a solution for a given
problem. For this, the extension provides a basic abstraction to
implement such prompts.

..  seealso::

    Read more about `techniques to improve reliability <https://github.com/openai/openai-cookbook/blob/main/techniques_to_improve_reliability.md>`__
    when it comes to prompt engineering.

..  php:namespace:: EliasHaeussler\Typo3Solver\ProblemSolving\Solution\Prompt

..  php:interface:: Prompt

    Basic abstraction to generate prompts for a given problem.
    Ideally, prompts are developed to match a specific model.

    ..  php:staticmethod:: create()

        Create a new instance of the prompt generator. This is
        mainly used on a low level basis where dependency injection
        is not available.

        :returns: An instance of the current prompt generator.

    ..  php:method:: generate($exception)

        Generate a prompt to provide a solution for the given
        exception.

        :param Throwable $exception: The exception to generate a prompt for
        :returntype: string

..  _default-prompt:

Default prompt
==============

The extension already ships a default prompt, targeting the GPT-3.5 model.

..  php:class:: DefaultPrompt

    Default prompt generator, mainly developed for the GPT-3.5 model,
    but can be used for other models as well. It passes the following
    information to the OpenAI model:

    -   Exception class
    -   Exception message
    -   File where the exception was triggered
    -   Line where the exception was triggered
    -   Code snippet of the line that triggered the exception
    -   Installation mode (composer or legacy)
    -   Installed TYPO3 version
    -   Installed PHP version

..  seealso::
    View the sources on GitHub:

    -   `Prompt <https://github.com/eliashaeussler/typo3-solver/blob/main/Classes/ProblemSolving/Solution/Prompt/Prompt.php>`__
    -   `DefaultPrompt <https://github.com/eliashaeussler/typo3-solver/blob/main/Classes/ProblemSolving/Solution/Prompt/DefaultPrompt.php>`__
        (`Fluid template <https://github.com/eliashaeussler/typo3-solver/blob/main/Resources/Private/Templates/Prompt/Default.html>`__)
