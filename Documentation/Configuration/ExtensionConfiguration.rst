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

        This setting only applies to the default solution providers.

..  _extconf-api-key:

..  confval:: api.key
    :type: string

    :ref:`API key <api-key>` for the configured AI provider.

    ..  attention::

        It is essential to configure the API key. Otherwise, requests to
        the AI provider won't be possible.

..  _extconf-attributes-model:

..  confval:: attributes.model
    :type: string
    :Default: `gpt-4o-mini`

    AI model to use (see :ref:`List available models <solver-list-models>`
    to show a list of available models).

    ..  seealso::

        -   `List of OpenAI models <https://platform.openai.com/docs/models>`__
        -   `List of Gemini models <https://ai.google.dev/gemini-api/docs/models/gemini>`__

..  _extconf-attributes-maxTokens:

..  confval:: attributes.maxTokens
    :type: integer
    :Default: `300`

    Maximum number of tokens to use per request to the configured AI provider.

    *Supported providers:*

    -   `OpenAI <https://platform.openai.com/docs/api-reference/chat/create#chat-create-max_completion_tokens>`__
    -   `Gemini <https://ai.google.dev/api/generate-content#FIELDS.max_output_tokens>`__

..  _extconf-attributes-temperature:

..  confval:: attributes.temperature
    :type: float
    :Default: `0.5`

    Temperature to use for completion requests.

    *Supported providers:*

    -   `OpenAI <https://platform.openai.com/docs/api-reference/chat/create#chat-create-temperature>`__
        (value must be between `0` and `1`)
    -   `Gemini <https://ai.google.dev/api/generate-content#FIELDS.temperature>`__
        (value must be between `0` and `2`)

..  _extconf-attributes-numberOfCompletions:

..  confval:: attributes.numberOfCompletions
    :type: integer
    :Default: `1`

    Number of completions to generate for each problem.

    *Supported providers:*

    -   `OpenAI <https://platform.openai.com/docs/api-reference/chat/create#chat-create-n>`__
    -   `Gemini <https://ai.google.dev/api/generate-content#FIELDS.candidateCount>`__
        (only `1` is supported at the moment)

..  _extconf-cache-lifetime:

..  confval:: cache.lifetime
    :type: integer
    :Default: `86400` *(= 1 day)*

    Lifetime in seconds of the solutions cache.

    ..  tip::

        Use `0` to disable caching.
