..  include:: /Includes.rst.txt

..  _contributing:

============
Contributing
============

Thanks for considering contributing to this extension! Since it is
an open source product, its successful further development depends
largely on improving and optimizing it together.

The development of this extension follows the official
`TYPO3 coding standards <https://github.com/TYPO3/coding-standards>`__.
To ensure the stability and cleanliness of the code, various code
quality tools are used and most components are covered with test
cases. In addition, we use `DDEV <https://ddev.readthedocs.io/en/stable/>`__
for local development. Make sure to set it up as described below. For
continuous integration, we use GitHub Actions.

..  _create-an-issue-first:

Create an issue first
=====================

Before you start working on the extension, please create an issue on
GitHub: https://github.com/eliashaeussler/typo3-solver/issues

Also, please check if there is already an issue on the topic you want
to address.

..  _contribution-workflow:

Contribution workflow
=====================

..  note::

    This extension follows `Semantic Versioning <https://semver.org/>`__.

..  _preparation:

Preparation
-----------

Clone the repository first:

..  code-block:: bash

    git clone https://github.com/eliashaeussler/typo3-solver.git
    cd typo3-solver

Now start DDEV:

..  code-block:: bash

    ddev start

Next, install all dependencies:

..  code-block:: bash

    ddev composer install
    ddev frontend install

You can access the DDEV site at https://typo3-ext-solver.ddev.site/.

..  _check-code-quality:

Check code quality
------------------

..  image:: https://github.com/eliashaeussler/typo3-solver/actions/workflows/cgl.yaml/badge.svg
    :target: https://github.com/eliashaeussler/typo3-solver/actions/workflows/cgl.yaml

..  _cgl-typo3:

TYPO3
~~~~~

..  code-block:: bash

    # All linters
    ddev composer lint

    # Specific linters
    ddev composer lint:composer
    ddev composer lint:editorconfig
    ddev composer lint:php

    # Fix all CGL issues
    ddev composer fix

    # Fix specific CGL issues
    ddev composer fix:composer
    ddev composer fix:editorconfig
    ddev composer fix:php

    # All static code analyzers
    ddev composer sca

    # Specific static code analyzers
    ddev composer sca:php

..  _cgl-frontend:

Frontend
~~~~~~~~

..  code-block:: bash

    # All linters
    ddev frontend run lint

    # Specific linters
    ddev frontend run lint:scss
    ddev frontend run lint:ts

    # Fix all CGL issues
    ddev frontend run fix

    # Fix specific CGL issues
    ddev frontend run fix:scss
    ddev frontend run fix:ts

..  _run-tests:

Run tests
---------

..  image:: https://github.com/eliashaeussler/typo3-solver/actions/workflows/tests.yaml/badge.svg
    :target: https://github.com/eliashaeussler/typo3-solver/actions/workflows/tests.yaml

..  rst-class:: d-inline-block mb-3

..  image:: https://img.shields.io/coverallsCoverage/github/eliashaeussler/typo3-solver?logo=coveralls
    :target: https://coveralls.io/github/eliashaeussler/typo3-solver

..  code-block:: bash

    # All tests
    ddev composer test

    # Specific tests
    ddev composer test:functional
    ddev composer test:unit

    # All tests with code coverage
    ddev composer test:coverage

    # Specific tests with code coverage
    ddev composer test:coverage:functional
    ddev composer test:coverage:unit

    # Merge code coverage of all test suites
    ddev composer test:coverage:merge

Code coverage reports are written to :file:`.build/coverage`. You can
open the last merged HTML report like follows:

..  code-block:: bash

    open .build/coverage/html/_merged/index.html

..  _build-documentation:

Build documentation
-------------------

..  code-block:: bash

    # Rebuild and open documentation
    composer docs

    # Build documentation (from cache)
    composer docs:build

    # Open rendered documentation
    composer docs:open

The built docs will be stored in :file:`.build/docs`.

..  _pull-request:

Pull Request
------------

Once you have finished your work, please **submit a pull request** and describe
what you've done: https://github.com/eliashaeussler/typo3-solver/pulls

Ideally, your PR references an issue describing the problem
you're trying to solve. All described code quality tools are automatically
executed on each pull request for all currently supported PHP versions and TYPO3
versions.
