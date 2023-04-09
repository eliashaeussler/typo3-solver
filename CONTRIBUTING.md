# Contributing

This project uses [DDEV][1] for local development. Make sure to set it up as
described below. For continuous integration, we use GitHub Actions.

## Preparation

```bash
# Clone repository
git clone https://github.com/eliashaeussler/typo3-solver.git
cd typo3-solver

# Start DDEV project
ddev start
```

You can access the DDEV site at <https://typo3-ext-solver.ddev.site/>.

## Run linters

```bash
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
```

## Run tests

```bash
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
```

### Test reports

Code coverage reports are written to `.build/coverage`. You can open the last
merged HTML report like follows:

```bash
open .build/coverage/html/_merged/index.html
```

## Submit a pull request

Once you have finished your work, please **submit a pull request** and describe
what you've done. Ideally, your PR references an issue describing the problem
you're trying to solve.

All described code quality tools are automatically executed on each pull request
for all currently supported PHP versions and TYPO3 versions. Take a look at the
appropriate [workflows][2] to get a detailed overview.

[1]: https://ddev.readthedocs.io/en/stable/
[2]: .github/workflows
