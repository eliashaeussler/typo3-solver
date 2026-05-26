..  include:: /Includes.rst.txt

..  image:: https://img.shields.io/coverallsCoverage/github/eliashaeussler/typo3-solver?logo=coveralls
    :target: https://coveralls.io/github/eliashaeussler/typo3-solver

..  image:: https://img.shields.io/github/actions/workflow/status/eliashaeussler/typo3-solver/ci.yaml?label=CI&logo=github
    :target: https://github.com/eliashaeussler/typo3-solver/actions/workflows/ci.yaml

..  _contributing:

==================
Contribution guide
==================

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

..  _preparation:

Preparation
===========

..  code-block:: bash

    # Clone repository
    git clone https://github.com/eliashaeussler/typo3-solver.git
    cd typo3-solver

    # Install dependencies
    composer install

..  _development-workflow:

Development workflow
====================

A typical contribution workflow looks like this:

..  rst-class:: bignums-xxl

    1.  Apply automatic fixes

        Use the following commands to normalize and format the code base:

        ..  code-block:: bash

            # Apply all automatic fixes
            composer fix

            # Apply specific fixes
            composer fix:composer
            composer fix:editorconfig
            composer fix:php

    2.  Run checks

        Use :bash:`composer check` to run the full code quality pipeline locally.
        This command bundles dependency analysis, static analysis, coding style checks,
        and Rector in dry-run mode so that potential refactorings can be reviewed
        without changing files.

        ..  code-block:: bash

            # Run all checks
            composer check

            # Run specific checks
            composer check:deps
            composer check:refactor
            composer check:static
            composer check:style

            # Run specific style checks
            composer check:style:composer
            composer check:style:editorconfig
            composer check:style:php

        ..  _refactorings:

    3.  Run refactorings

        Refactorings are intentionally separated from regular checks because they may
        change the code base.

        ..  code-block:: bash

            # Run all configured refactorings
            composer refactor

            # Run specific refactorings
            composer refactor:php

    4.  Run tests

        Run the full test suite before opening a pull request:

        ..  code-block:: bash

            # Run all tests
            composer test
            composer test:coverage

            # Run functional tests
            composer test:functional
            composer test:functional:coverage

            # Run unit tests
            composer test:unit
            composer test:unit:coverage

            # Merge coverage reports
            composer test:merge-coverage

..  _coverage-reports:

Coverage reports
================

Code coverage reports are written to `.build/coverage`. Open the latest merge
HTML report with:

..  code-block:: bash

    open .build/coverage/html/_merged/index.html

..  _pull-requests:

Pull requests
=============

Once the changes are ready, please
`submit a pull request <https://github.com/eliashaeussler/typo3-solver/compare>`__
and describe what was changed and why. Ideally, the pull request references an
issue that describes the problem being solved.

All documented code quality tools are executed automatically for pull requests
across the currently supported PHP versions. For details, refer to the GitHub
Actions workflows.
