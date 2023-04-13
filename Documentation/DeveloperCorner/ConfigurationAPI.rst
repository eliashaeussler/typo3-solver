..  include:: /Includes.rst.txt

..  _configuration-api:

=================
Configuration API
=================

In order to access the :ref:`extension configuration <extension-configuration>`,
a slim PHP API exists. Each configuration option is accessible by
an appropriate class method.

..  php:namespace:: EliasHaeussler\Typo3Solver\Configuration

..  php:class:: Configuration

    API to access all available extension configuration options.

    ..  php:method:: getAPIKey()

        Get the configured :ref:`OpenAI API key <extconf-api-key>`.

        :returntype: string|null

    ..  php:method:: getModel()

        Get the configured :ref:`OpenAI model <extconf-attributes-model>`.

        :returntype: string

    ..  php:method:: getMaxTokens()

        Get the configured :ref:`maximum number of tokens <extconf-attributes-maxTokens>`.

        :returntype: int

    ..  php:method:: getTemperature()

        Get the configured :ref:`temperature <extconf-attributes-temperature>`.

        :returntype: float

    ..  php:method:: getNumberOfCompletions()

        Get the configured :ref:`number of completions <extconf-attributes-numberOfCompletions>`.

        :returntype: int

    ..  php:method:: getCacheLifetime()

        Get the configured :ref:`cache lifetime <extconf-cache-lifetime>`.

        :returntype: int

    ..  php:method:: getProvider()

        Get the configured :ref:`solution provider <extconf-provider>`.

        :returntype: :php:interface:`EliasHaeussler\\Typo3Solver\\ProblemSolving\\Solution\\Provider\\SolutionProvider`

    ..  php:method:: getPrompt()

        Get the configured :ref:`prompt <extconf-prompt>`.

        :returntype: :php:interface:`EliasHaeussler\\Typo3Solver\\ProblemSolving\\Solution\\Prompt\\Prompt`

    ..  php:method:: getIgnoredCodes()

        Get the configured :ref:`list of exception codes to ignore <extconf-ignoredCodes>`.

        :returntype: array

..  seealso::

    View the sources on GitHub:

    -   `Configuration <https://github.com/eliashaeussler/typo3-solver/blob/main/Classes/Configuration/Configuration.php>`__
