..  include:: /Includes.rst.txt

..  _extension-configuration:

=======================
Extension configuration
=======================

The extension currently provides the following configuration options:

..  _extconf-provider:

..  confval:: provider
    :type: string (FQCN)
    :Default: :php:class:`EliasHaeussler\\Typo3Solver\\ProblemSolving\\Solution\\Provider\\OpenAISolutionProvider`

    Default solution provider to be used for problem solving.

    ..  note::

        Custom solutions providers must implement
        :php:interface:`EliasHaeussler\\Typo3Solver\\ProblemSolving\\Solution\\Provider\\SolutionProvider`.

..  _extconf-prompt:

..  confval:: prompt
    :name: extconf-prompt
    :type: string (FQCN)
    :Default: :php:class:`EliasHaeussler\\Typo3Solver\\ProblemSolving\\Solution\\Prompt\\DefaultPrompt`

    Default prompt to be used for problem solving.

    ..  note::

        Custom prompts must implement
        :php:interface:`EliasHaeussler\\Typo3Solver\\ProblemSolving\\Solution\\Prompt\\Prompt`.

..  _extconf-ignoredCodes:

..  confval:: ignoredCodes
    :type: string (comma-separated list)

    Comma-separated list of exception codes to ignore during problem solving.
    Once a raised exception matches with the configured exception codes, the
    solution provider won't provide a solution and is simply ignored.

    ..  note::

        This setting only applies to the default
        :php:class:`EliasHaeussler\\Typo3Solver\\ProblemSolving\\Solution\\Provider\\OpenAISolutionProvider`.

..  _extconf-api-key:

..  confval:: api.key
    :type: string

    :ref:`API key <api-key>` for OpenAI requests.

    ..  attention::

        It is essential to configure the API key. Otherwise, requests to OpenAI
        won't be possible.

..  _extconf-attributes-model:

..  confval:: attributes.model
    :type: string
    :Default: `gpt-4o-mini`

    `OpenAI model <https://platform.openai.com/docs/models>`__ to use (see
    :ref:`List available models <solver-list-models>` to show a list of available
    models).

..  _extconf-attributes-maxTokens:

..  confval:: attributes.maxTokens
    :type: integer
    :Default: `300`

    `Maximum number of tokens <https://platform.openai.com/docs/api-reference/chat/create#chat-create-max_completion_tokens>`__
    to use per request to OpenAI.

..  _extconf-attributes-temperature:

..  confval:: attributes.temperature
    :type: float
    :Default: `0.5`

    `Temperature <https://platform.openai.com/docs/api-reference/chat/create#chat/create-temperature>`__
    to use for OpenAI completion requests (must be a value between `0` and `1`).

..  _extconf-attributes-numberOfCompletions:

..  confval:: attributes.numberOfCompletions
    :type: integer
    :Default: `1`

    `Number of completions <https://platform.openai.com/docs/api-reference/chat/create#chat/create-n>`__
    to generate for each problem.

..  _extconf-cache-lifetime:

..  confval:: cache.lifetime
    :type: integer
    :Default: `86400` *(= 1 day)*

    Lifetime in seconds of the solutions cache.

    ..  tip::

        Use `0` to disable caching.
