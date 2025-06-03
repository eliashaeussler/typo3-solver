..  include:: /Includes.rst.txt

..  image:: https://img.shields.io/coverallsCoverage/github/eliashaeussler/typo3-solver?logo=coveralls
    :target: https://coveralls.io/github/eliashaeussler/typo3-solver

..  image:: https://img.shields.io/codeclimate/maintainability/eliashaeussler/typo3-solver?logo=codeclimate
    :target: https://codeclimate.com/github/eliashaeussler/typo3-solver/maintainability

..  image:: https://img.shields.io/github/actions/workflow/status/eliashaeussler/typo3-solver/ci.yaml?label=CI&logo=github
    :target: https://github.com/eliashaeussler/typo3-solver/actions/workflows/ci.yaml

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

..  tip::

    There's also a dedicated DDEV command to manage TER libraries located at
    :file:`Resources/Private/Libs/Build`. Run :bash:`ddev libs <command>` with
    any available Composer command, e.g. :bash:`ddev libs install`.

..  _analyze-code:

Analyze code
------------

..  _check-code-quality:

..  code-block:: bash

    # All analyzers
    ddev cgl analyze

    # Specific analyzers
    ddev cgl analyze:dependencies

Check code quality
------------------

..  _cgl-typo3:

TYPO3
~~~~~

..  code-block:: bash

    # All linters
    ddev cgl lint

    # Specific linters
    ddev cgl lint:composer
    ddev cgl lint:editorconfig
    ddev cgl lint:php

    # Fix all CGL issues
    ddev cgl fix

    # Fix specific CGL issues
    ddev cgl fix:composer
    ddev cgl fix:editorconfig
    ddev cgl fix:php

    # All static code analyzers
    ddev cgl sca

    # Specific static code analyzers
    ddev cgl sca:php

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

..  code-block:: bash

    # All tests
    ddev test

    # Specific tests
    ddev test functional
    ddev test unit

    # All tests with code coverage
    ddev test coverage

    # Specific tests with code coverage
    ddev test coverage:functional
    ddev test coverage:unit

    # Merge code coverage of all test suites
    ddev test coverage:merge

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
