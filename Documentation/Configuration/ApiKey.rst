..  include:: /Includes.rst.txt

..  _api-key:

=======
API key
=======

You need an `API key <https://platform.openai.com/account/api-keys>`__ to
perform requests at OpenAI. Once generated, the key must be configured in
the :ref:`extension configuration <extconf-api-key>`.

Follow these steps to create and use an OpenAI API key:

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

    Paste the generated API key in the extension configuration
    :ref:`extconf-api-key`. This can either be done in the TYPO3 backend or
    directly in your :file:`config/system/settings.php` (formerly
    :file:`typo3conf/ext/LocalConfiguration.php`) file.

4.  Keep the key secret!

    **Don't share this key with anyone else!** Keep in mind that everyone
    with access to this key will be able to perform requests with your
    OpenAI account. This can be very cost intensive as request are not free.
