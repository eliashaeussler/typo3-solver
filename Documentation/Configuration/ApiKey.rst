..  include:: /Includes.rst.txt

..  _api-key:

=======
API key
=======

Please follow the instructions below to retrieve an API key for the
specific AI provider in use. Once generated, the key must be configured
in the :ref:`extension configuration <extconf-api-key>`:

Paste the generated API key in the extension configuration
:ref:`extconf-api-key`. This can either be done in the TYPO3 backend or
directly in your :file:`config/system/settings.php` (formerly
:file:`typo3conf/ext/LocalConfiguration.php`) file.

..  _api-key-open-ai:

OpenAI
======

Follow these steps to create and use an OpenAI
`API key <https://platform.openai.com/account/api-keys>`__:

..  rst-class:: bignums-xxl

1.  Log in with your OpenAI account

    Head over to https://platform.openai.com/account/ and log in with your
    OpenAI account. If you don't have an account yet, you can sign up on
    this page as well.

2.  Create API key

    Head over to the :guilabel:`API Keys` section. You should see all your
    API keys listed, if you already generated ones.

    Click on the :guilabel:`Create new secret key` button to create a new
    API key. Copy the key to your clipboard or safe it elsewhere.

    ..  attention::

        You won't be able to display the key again once you closed the
        page.

3.  Add extension configuration

    See instructions above to learn how to configure the API key.

..  _api-key-google-gemini:

Google Gemini
=============

Follow these steps to create and use a Google Gemini
`API key <https://aistudio.google.com/apikey>`__:

..  rst-class:: bignums-xxl

1.  Log in with your Google account

    Head over to https://aistudio.google.com/apikey and log in with your
    Google account. If you don't have an account yet, you can sign up on
    this page as well.

2.  Create API key

    Click on the :guilabel:`Create API key` button and select a Google Cloud
    project where to assign the new API key. Copy the key to your clipboard
    or safe it elsewhere.

    ..  attention::

        You won't be able to display the key again once you closed the
        page.

3.  Add extension configuration

    See instructions above to learn how to configure the API key.
